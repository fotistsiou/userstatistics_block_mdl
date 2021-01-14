<?php

class block_userstatistics extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_userstatistics');
    }

    function get_content() {
        global $DB, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';

        $logs = $DB->get_records('logstore_standard_log');

        $sumlogin = [];
        $sumlogout = [];
        $totals = [];
        $totaltime = 0;

        // Εύρεση όλων των login και logout του χρήστη που είναι συνδεδεμένος.
        foreach ($logs as $log) {
            if ($log->userid == $USER->id) {
                if ($log->action == 'loggedin') {
                    $logintime = $log->timecreated;
                    array_push($sumlogin, $logintime);
                    $this->content->text .= "Login time: ". userdate($logintime) . "<br>";
                } elseif ($log->action == 'loggedout'){
                    $logouttime = $log->timecreated;
                    array_push($sumlogout, $logouttime);
                    $this->content->text .= "Logout time: " . userdate($logouttime) . "<br>";
                }
            }
        }

        // Εύρεση όλων των επιμέρους διαστημάτων που ο χρήστης ήταν ενεργός στην πλατφόρμα και εισαγωγή τους στο array $totals.
        for ($h = 0; $h < count($sumlogout); $h++) {
            $dif = $sumlogout[$h] - $sumlogin[$h];
            array_push($totals, $dif);
        }

        // Άθροιση όλων τον επιμέρους διαστημάτων που ο χρήστης ήταν ενεργός στην πλατφόρμα. 
        foreach ($totals as $total) {
            $totaltime +=  $total;
        }

        // Εμφάνιση του τελικού χρόνου του χρήστη στην πλατφόρμα.
        if (($totaltime/60) < 60) {
            $this->content->text .= "Total time: " . round(($totaltime/60)) . " minutes";
        } else {
            $this->content->text .= "Total time: " . round(($totaltime/3600)) . " hours";
        } 

        return $this->content;
    }

}