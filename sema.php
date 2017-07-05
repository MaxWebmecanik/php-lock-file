<?php

$fp = fopen("lock.txt", "c+");

echo "Putting down lock...\n";

if (!flock($fp, LOCK_EX | LOCK_NB)) {

    echo "Couldn't get the lock!\n";

} else {

    echo "Got the lock!\n";

    echo "Holding the lock...\n";
    sleep(5);
    echo "Moving on.\n";

    $stored_pid = stream_get_contents($fp);

    if ($stored_pid === '') {

        echo "There is no stored PID.\n";

    } else {

        echo "Got stored PID $stored_pid.\n";

        if (posix_getpgid($stored_pid) !== FALSE) {

            echo "There is a process currently running with PID $stored_pid.\n";
            return;

        } else {

            echo "There is no process currently running with PID $stored_pid.\n";

        }

    }

    ftruncate($fp, 0);
    rewind($fp);
    echo "Truncated lock file.\n";

    $current_pid = getmypid();
    echo "Current PID is $current_pid.\n";

    fwrite($fp, $current_pid);
    echo "Wrote current PID to lock file.\n";

    echo "Flushing before releasing lock...\n";
    fflush($fp);

    flock($fp, LOCK_UN);
    fclose($fp);
    echo "Released the lock.\n";

    echo "Going to sleep for a while...\n";
    sleep(25);
    echo "Woke up.\n";

}
