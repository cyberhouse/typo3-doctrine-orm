<?php
namespace Cyberhouse\DoctrineORM\Database;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Pretty print CREATE Statements in a TYPO3 readable way
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class CreateTablePrinter
{
    /**
     * @var IdentifierQuotes
     */
    private $quotes;

    public function __construct()
    {
        $this->quotes = GeneralUtility::makeInstance(IdentifierQuotes::class);
    }

    public function getStatement(string $src, bool $withComment = true): string
    {
        $pos = strpos($src, '(');
        $table = $this->quotes->remove(substr($src, 13, $pos - 13));

        $target = [];

        if ($withComment) {
            $target[] = '#';
            $target[] = '# Table structure for table \'' . $table . '\'';
            $target[] = '#';
        }

        $target[] = 'CREATE TABLE ' . $table . ' (';

        $numOpen = 1;
        $inBraces = false;
        $buffer = '';

        while (++$pos < strlen($src)) {
            $char = $src[$pos];

            if ($char === '(') {
                $numOpen++;
            } elseif ($char === ')') {
                $numOpen--;
            }

            switch (true) {
                case !$inBraces && $char === ',':
                    $target[] = '  ' . trim($buffer) . ',';
                    $buffer = '';
                    break;

                case !$inBraces && $char === '(':
                    $buffer .= $char;
                    $inBraces = true;
                    break;

                case $inBraces && $char === ')':
                    $buffer .= $char;
                    $inBraces = false;
                    break;

                case !$inBraces && $char === ')' && !$numOpen:
                    $buffer .= "\n" . $char;
                    break;

                default:
                    $buffer .= $char;
            }
        }

        if (strlen($buffer) > 1) {
            $target[] = '  ' . trim(rtrim($buffer, ' ;'));
        }

        $sql = implode(LF, $target) . ';';

        return $sql . LF;
    }
}
