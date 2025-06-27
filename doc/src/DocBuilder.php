<?php

use AvpLab\PhpHtmlBuilder;
use phpowermove\docblock\Docblock;

class DocBuilder
{
    protected array     $sections;
    protected string    $dirBuild;
    protected string    $fileBuild;
    protected Parsedown $parsedown;
    protected array     $methods = [];

    protected string $cntJs      = '';
    protected string $cntCss     = '';
    protected string $cntAbout   = '';
    protected string $cntSidebar = '';
    protected string $cntMethods = '';

    public function __construct(array $parts, string $dirBuild)
    {
        $this->sections = $parts;
        $this->dirBuild = $dirBuild;
        $this->fileBuild = $dirBuild . '/index.html';
        $this->parsedown = new ParsedownExtra();
    }

    public function execute(): void
    {
        $this->log('--> Очистка директории сборки');
        $this->recreateBuildDir();

        $this->log('--> Заполнение данных');
        $this->fetch();

        $this->log('--> Рендер и сохранение файла');
        $cnt = $this->renderTpl();
        file_put_contents($this->fileBuild, $cnt);

        $this->log('');
        $this->log($this->fileBuild);
    }

    protected function fetch(): void
    {
        $this->fetchAssets();
        $this->fetchAbout();
        $this->fetchMethods();
        $this->renderJsData();
        $this->renderCntSidebar();
        $this->renderCntMethods();
    }

    protected function fetchAbout(): void
    {
        $cnt = file_get_contents(__DIR__ . '/about.md');
        $this->cntAbout = $this->parsedown->text($cnt);
    }

    protected function fetchAssets(): void
    {
        $dirAssets = __DIR__ . '/asset/';
        $jsFiles = [
            'prism.js',
            'js.js',
        ];
        $cssFiles = [
            'prism.css',
            'style.css',
        ];

        foreach ($jsFiles as $jsFile) {
            $this->cntJs .= '/* файл ' . $jsFile . ' */' . PHP_EOL;
            $this->cntJs .= file_get_contents($dirAssets . $jsFile);
            $this->cntJs .= PHP_EOL;
        }

        foreach ($cssFiles as $cssFile) {
            $this->cntCss .= '/* файл ' . $cssFile . ' */' . PHP_EOL;
            $cnt = file_get_contents($dirAssets . $cssFile);
            $cnt = preg_replace('/' . PHP_EOL . '/', ' ', $cnt);
            $cnt = preg_replace('/\s{2,}/', ' ', $cnt);
            $this->cntCss .= $cnt;
            $this->cntCss .= PHP_EOL;
        }
    }

    protected function renderCntMethods(): void
    {
        $builder = new PhpHtmlBuilder();

        foreach ($this->methods as $section) {
            $builder
                ->section()->setClass('section')->setId($section['id'])
                ->h3($section['title'])->setClass('section__title')->end()
                ->div()->setClass('methods');

            foreach ($section['items'] as $item) {
                $builder->div()->setClass('method');

                if (!$item['isImplementParent']) {
                    $builder->h4($item['title'])->setId($item['id'])->setClass('method__title')->end();
                }

                $cnt = $this->renderMethodImplementations($item['implementations']);
                $builder->div($cnt)->setClass('method__cnt')->end();

                $builder->end();
            }

            $builder->end()->end();
        }

        $this->cntMethods = $builder->build();
    }

    protected function renderMethodImplementations(array $items): string
    {
        $builder = new PhpHtmlBuilder();
        $builderTitles = new PhpHtmlBuilder();
        $builderTitles->div()->setClass('method__tabs-titles');
        $builderCnt = new PhpHtmlBuilder();
        $builderCnt->div()->setClass('method__tabs-cnts');

        foreach ($items as $index => $item) {
            $html = $this->docBlockToHtml($item);
            $modClass = $index === 0 ? '_active' : '';

            $builderTitles->div($item['className'])->setClass('method__tabs-title js-tabs-title ' . $modClass)->setDataNumber($index)->end();
            $builderCnt->div($html)->setClass('method__tabs-cnt js-tabs-cnt ' . $modClass)->setDataNumber($index)->end();
        }

        $builder
            ->div()->setClass('method__tabs js-tabs')
            ->addHtml($builderTitles->end()->build())
            ->addHtml($builderCnt->end()->build())
            ->end();

        return $builder->build();
    }

    protected function renderCntSidebar(): void
    {
        $builder = new PhpHtmlBuilder();
        $builder->ul()->setClass('sidebar__nav');

        foreach ($this->methods as $item) {
            $builder
                ->li()
                ->a($item['title'])->setHref('#' . $item['id'])->end()
                ->end();
        }

        $builder->end();
        $this->cntSidebar = $builder->build();
    }

    protected function fetchMethods(): void
    {
        foreach ($this->sections as $sectionTitle => $methods) {
            $section = [
                'title' => $sectionTitle,
                'id'    => 'section_' . $this->idFromTitle($sectionTitle),
                'items' => [],
            ];

            foreach ($methods as $methodTitle => $funcs) {
                $isImplementParent = is_numeric($methodTitle);
                $title = $isImplementParent ? trim($sectionTitle) : trim($methodTitle);

                $section['items'][] = [
                    'title'             => $title,
                    'id'                => $this->idFromTitle($title),
                    'isImplementParent' => $isImplementParent,
                    'implementations'   => $this->fetchMethodsImplementations($funcs),
                ];
            }

            $this->methods[] = $section;
        }
    }

    protected function renderJsData(): void
    {
        $methods = [];

        foreach ($this->methods as $section) {
            $items = [];
            $sectionTitleSearch = strtolower($section['title']);

            foreach ($section['items'] as $item) {
                if ($item['isImplementParent']) {
                    continue;
                }

                $titleSearch = strtolower($item['title']);
                $titleSearch = str_replace($sectionTitleSearch, '', $titleSearch);
                $titleSearch = trim($titleSearch);

                $items[] = [
                    'title'       => $item['title'],
                    'titleSearch' => $titleSearch,
                    'id'          => $item['id'],
                ];
            }

            $methods[] = [
                'title'       => $section['title'],
                'titleSearch' => $sectionTitleSearch,
                'id'          => $section['id'],
                'items'       => $items,
            ];
        }

        $this->cntJs .= PHP_EOL;
        $this->cntJs .= '/* Autocomplete data */' . PHP_EOL;
        $this->cntJs .= 'var methods = ' . $this->arrayToJsObject($methods) . ';';
    }

    protected function arrayToJsObject(array $arr): string
    {
        $res = '';
        $parts = [];

        if (array_is_list($arr)) {
            $res .= '[';
            foreach ($arr as $item) {
                $parts[] = $this->valueToJs($item);
            }
            $res .= implode(',', $parts);
            $res .= ']';
        } else {
            $res .= '{';
            foreach ($arr as $key => $item) {
                $parts[] = "'" . $key . "':" . $this->valueToJs($item);
            }
            $res .= implode(',', $parts);
            $res .= '}';
        }

        return $res;
    }

    protected function valueToJs(array|string|int|float|bool|null $value): string
    {
        return match (true) {
            is_array($value)  => $this->arrayToJsObject($value),
            is_string($value) => '"' . $value . '"',
            default           => $value,
        };
    }

    protected function fetchMethodsImplementations(array $parts): array
    {
        $res = [];

        foreach ($parts as $func) {
            $reflection = (new ReflectionFunction($func));
            $classNameParts = explode('\\', $reflection->getClosureScopeClass()->getName());
            $classNameParts = array_slice($classNameParts, 3);

            $className = match (true) {
                in_array('Chain', $classNameParts)     => 'Chain',
                in_array('ChainFunc', $classNameParts) => 'ChainFunc',
                in_array('Func', $classNameParts)      => 'Func',
            };

            $doc = $reflection->getDocComment();

            $res[] = [
                'className'  => $className,
                'methodName' => $reflection->getName(),
                'doc'        => $doc,
            ];
        }

        return $res;
    }

    protected function idFromTitle(string $title): string
    {
        $res = strtolower($title);

        return str_replace(' ', '_', $res);
    }

    protected function docBlockToHtml(array $data): string
    {
        $docblock = new Docblock($data['doc']);

        $definition = $this->buildDocblockDefinition($data['methodName'], $docblock);
        $desc = $this->buildDocblockDescription($docblock);
        $links = $this->buildDocblockLinks($docblock);

        return $definition . $desc . $links;
    }

    protected function buildDocblockDescription(Docblock $docblock): string
    {
        $descShort = $this->parsedown->text($docblock->getShortDescription());
        $descLong = $this->parsedown->text($docblock->getLongDescription());
        $desc = $descShort . $descLong;
        $desc = str_replace('<pre>', '<pre class="language-php">', $desc);

//        $desc = preg_replace('/<code>(true|false)<\/code>/', '<code class="token boolean">$1</code>', $desc);
//        $desc = preg_replace('/<code>\$(.*)<\/code>/U', '<code class="token variable">\$$1</code>', $desc);
//        $desc = preg_replace('/<code>(\d*)<\/code>/', '<code class="token number">$1</code>', $desc);
//        $desc = preg_replace('/<code>\'(.*)\'<\/code>/', '<code class="token string single-quoted-string">\'$1\'</code>', $desc);

        return $desc;
    }

    protected function buildDocblockDefinition(string $title, Docblock $docblock): string
    {
        $b = new PhpHtmlBuilder();

        $b->pre()->setClass('language-php method__definition');
        $b->span($title)->setClass('token function')->end();
        $b->span('(')->setClass('token punctuation')->end();

        $params = $docblock->getTags('param');
        foreach ($params as $index => $param) {
            $varName = '$' . $param->getVariable();
            if ($param->isVariadic()) {
                $varName = '...' . $varName;
            }
            $type = $param->getType() . ' ';
            $desc = $param->getDescription();

            $b->span($type)->setClass('token keyword type-hint')->end();
            $b->span($varName)->setClass('token variable')->end();
            if (!empty($desc)) {
                $b->span(' ' . $desc)->setClass('token comment')->end();
            }

            if ($index < count($params) - 1) {
                $b->span(', ')->setClass('token punctuation')->end();
            }
        }

        $b->span(')')->setClass('token punctuation')->end();

        foreach ($docblock->getTags('return') as $item) {
            $type = $item->getType();
            $b->span(': ')->setClass('token punctuation')->end();
            $b->span($type)->setClass('token tag')->end();
        }

        $b->end();

        return $b->build();
    }

    protected function buildDocblockLinks(Docblock $docblock): string
    {
        $builder = new PhpHtmlBuilder();

        $links = $docblock->getTags('link');
        if (count($links) > 0) {
            $builder
                ->div('')->setClass('method__links')
                ->h5('Ссылки')->setClass('method__links-title')->end();

            foreach ($links as $item) {
                $builder->a($item->getDescription())->setClass('method__link')->setHref($item->getUrl())->setTarget('_blank')->end();
            }

            $builder->end();
        }

        return $builder->build();
    }

    protected function recreateBuildDir(): void
    {
        foreach (scandir($this->dirBuild) as $item) {
            if ($item !== '.' && $item !== '..') {
                unlink($this->dirBuild . '/' . $item);
            }
        };
        rmdir($this->dirBuild);

        mkdir($this->dirBuild, 0777, true);
    }

    protected function log(string $msg): void
    {
        print_r($msg);
        echo PHP_EOL;
    }

    protected function renderTpl(): string
    {
        return <<<TPL
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Progerplace Chain</title>
    <style>
    $this->cntCss    
    </style>
</head>
<body>
<div id="about"></div>

<div class="main-grid">
    <div class="main-grid__sidebar">
        <div class="sidebar__search">
            <input type="text" class="sidebar__search-inp js-search-input" placeholder="Поиск по названию метода">
            <div class="sidebar__search-clear js-search-input-clear"></div>
        </div>
        <div class="sidebar__main">        
            <div class="sidebar__methods js-sidebar-methods">
                <div class="sidebar__methods-section">
                    <div class="sidebar__methods-section-title">Информация</div>
                    <a href="#about">Описание</a>
                    <a href="#sozdanie_obekta">Создание и экспорт</a>
                    <a href="#rabota_s_dochernimi_elementami">Дочерние элементы</a>
                    <a href="#ispolzovanie_aliasov">Алиасы</a>
                </div>
                <div class="sidebar__methods-section">
                    <h2 class="sidebar__methods-section-title">Методы</h2>                
                    $this->cntSidebar
                </div>
            </div>        
            <div class="sidebar__searched js-sidebar-searched">$this->cntSidebar</div>        
        </div>
    </div>
    <div class="main-grid__cnt">
        <section  class="section">    
            <h1 id="about" class="section__title">Progerplace chain</h1>
            <div class="about__desc">$this->cntAbout</div>
        </section>  
                    
        <div class="methods-title">Методы</div>
        <div id="methods"></div>
        $this->cntMethods
    </div>
</div>

<script>
$this->cntJs
</script>
</body>
</html>
TPL;
    }
}