<?php

require_once('BaseTest.php');

class OrganisationTest extends BaseTest
{
    public function testGetAllOrganisations()
    {
        $organisations = $this->client->organisation->getAll();
        foreach($organisations as $organisation)
        {
            $this->assertInstanceOf('SpryngPaymentsApiPhp\Object\Organisation', $organisation);
        }
    }
}