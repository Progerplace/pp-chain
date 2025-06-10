<?php /** @noinspection PhpUnhandledExceptionInspection */

trait Operations
{
    protected function assertEqualsMultiple(...$vals): void
    {
        if (count($vals) < 2) {
            throw new Error('Количество элементов для сравнения должно быть больше двух');
        }

        $expected = $vals[0];
        foreach ($vals as $index => $val) {
            if ($index === 0) {
                continue;
            }

            $this->assertEquals($expected, $val);
        }
    }
}