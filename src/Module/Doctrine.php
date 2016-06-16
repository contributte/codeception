<?php

/*
 * This file is part of the Arachne
 *
 * Copyright (c) Jáchym Toušek (enumag@gmail.com)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Arachne\Codeception\Module;

use Codeception\Exception\ModuleConfigException;
use Codeception\Module;
use Codeception\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;

class Doctrine extends Module
{
    public function _before(TestCase $test)
    {
        if ($this->config['dump']) {
            $em = $this->getModule(Nette::class)->grabService(EntityManagerInterface::class);
            $connection = $em->getConnection();
            $generator = $this->load(file_get_contents($this->config['dump']));

            try {
                foreach ($generator as $command) {
                    $stmt = $connection->prepare($command);
                    if (!$stmt->execute()) {
                        $error = $stmt->errorInfo();
                        throw new ModuleConfigException(__CLASS__, $error[2]);
                    }
                    $stmt->closeCursor();
                }
            } catch (PDOException $e) {
                throw new ModuleConfigException(__CLASS__, $e->getMessage(), $e);
            }
        }
    }

    public function load($sql)
    {
        $sql = explode("\n", preg_replace('%/\*(?!!\d+)(?:(?!\*/).)*\*/%s', '', $sql));
        $query = '';
        $delimiter = ';';
        $delimiterLength = 1;
        foreach ($sql as $sqlLine) {
            if (preg_match('/DELIMITER ([\;\$\|\\\\]+)/i', $sqlLine, $match)) {
                $delimiter = $match[1];
                $delimiterLength = strlen($delimiter);
                continue;
            }
            if (trim($sqlLine) == '' || trim($sqlLine) == ';' || preg_match('~^((--.*?)|(#))~s', $sqlLine)) {
                continue;
            }
            $query .= "\n".rtrim($sqlLine);
            if (substr($query, -1 * $delimiterLength, $delimiterLength) == $delimiter) {
                yield substr($query, 0, -1 * $delimiterLength);
                $query = '';
            }
        }
    }
}
