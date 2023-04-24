<?php

namespace Contentify\Jobs;

use ChrisKonnertz\Jobs\AbstractJob; // Import the AbstractJob class from the third-party package
use Contentify\UserActivities; // Import the UserActivities class

class DeleteUserActivitiesJob extends AbstractJob
{

    /**
     * Execute the job.
     *
     * @param int|null $executedAt
     * @return void
     */
    public function run(?int $executedAt = null): void
    {
        $userActivities = new UserActivities(); // Create an instance of UserActivities class
        $userActivities->deleteOld(); // Call the deleteOld() method on UserActivities instance
    }
}