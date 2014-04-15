<?php

require_once 'vendor/autoload.php';

class PrettyCron
{
    protected $cronLines = array();

    public function __construct($crontab)
    {
        if ($crontab != '') {
            $crontabLines = $this->getLines($crontab);

            if (count($crontabLines) > 0) {
                foreach ($crontabLines as $index => $line) {
                    if (trim($line) != '') {
                        try {
                            $this->cronLines[$index]['cron_expression'] = $this->getCronExpression($line);
                            $this->cronLines[$index]['crontab_line'] = $line;
                        } catch (Exception $e) { echo "Bad Line: " . $line;}
                    }
                }
            }
        }
    }

    /**
     * Build array of lines from the crontab string
     *
     * @param  type  $crontab
     * @return array
     */
    protected function getLines($crontab)
    {
        return explode(PHP_EOL, $crontab);
    }

    protected function getCronExpression($line)
    {
        $cronLine = "";

        try {
            $cronLine = Cron\CronExpression::factory($this->getDateTime($line));
        } catch (Exception $e) {}

        return $cronLine;
    }

    protected function getDateTime($line)
    {
        $characters = explode(" ", $line);

        // If this is a predefined scheduling definition, just return as is
        // CronExpression knows how to handle these
        if (substr($characters[0], 0, 1) == '@')
            return trim($characters[0]);

        $dateTime = "";

        if (count($characters) >= 4 && trim($characters[0]) !== '#') {
            $dateTime = trim($characters[0]) . " " .
                        trim($characters[1]) . " " .
                        trim($characters[2]) . " " .
                        trim($characters[3]) . " " .
                        trim($characters[4]) . " ";
        }

        return $dateTime;
    }

    public function getCronLines()
    {
        return $this->cronLines;
    }

    public function getCronLinesByDate()
    {
        $sorted = array();

        foreach ($this->cronLines as $index => $cronLine) {
            if ($cronLine['cron_expression'] instanceof \Cron\CronExpression) {
                try {
                    $sorted[$cronLine['cron_expression']->getNextRunDate()->format('Y-m-d H:i:s') . " " . $index] = $cronLine;
                } catch (Exception $e) {}
            }
        }

        ksort($sorted);

        return $sorted;
    }

    public function getGroupedByTimeDay()
    {
        $grouped = array();

        foreach ($this->cronLines as $index => $cronLine) {
            if ($cronLine['cron_expression'] instanceof \Cron\CronExpression) {
                try {
                    if (!isset($grouped[$cronLine['cron_expression']->getNextRunDate()->format('Y-m-d H:i:s')])) {
                        $grouped[$cronLine['cron_expression']->getNextRunDate()->format('Y-m-d H:i:s')] = array('count' => 0,
                                                                                                                'time' => $cronLine['cron_expression']->getNextRunDate()->format('H:i:s'));
                    }

                    $grouped[$cronLine['cron_expression']->getNextRunDate()->format('Y-m-d H:i:s')]['count']++;
                } catch (Exception $e) {}
            }
        }

        ksort($grouped);

        //reindex
        $reindexedGrouped = array();
        foreach ($grouped as $g) {
            $reindexedGrouped[] = $g;
        }

        return $reindexedGrouped;
    }
}
