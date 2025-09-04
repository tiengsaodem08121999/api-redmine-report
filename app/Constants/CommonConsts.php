<?php

namespace App\Constants;

class CommonConsts
{
    const Status = [
        'New'         => 1,
        'In_Progress' => 2,
        'Resolved'    => 3,
        'Feedback'    => 4,
        'Closed'      => 5,
        'Canceled'    => 7,
        'Pending'     => 8,
    ];

    const Tracker = [
        'Task'   => 5,
        'Bug'    => 1,
        'Q&A'    => 2,
        'CR'     => 4,
        'Issue'  => 6,
        'Risk'   => 7,
        'Report' => 8,
        'QA-Task' => 28,
    ];
}