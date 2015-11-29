<?php

namespace SAML2\Assertion\Validation\ConstraintValidator;

use SAML2\ControlledTimeTest;
use SAML2\Assertion\Validation\Result;

use \Mockery as m;

/**
 * Because we're mocking a static call, we have to run it in separate processes so as to no contaminate the other
 * tests.
 *
 * @runTestsInSeparateProcesses
 */
class SubjectConfirmationNotBeforeTest extends ControlledTimeTest
{
    /**
     * @var \Mockery\MockInterface
     */
    private $subjectConfirmation;

    /**
     * @var \Mockery\MockInterface
     */
    private $subjectConfirmationData;

    public function setUp()
    {
        parent::setUp();
        $this->subjectConfirmation = m::mock('SAML2_XML_saml_SubjectConfirmation');
        $this->subjectConfirmationData = m::mock('SAML2_XML_saml_SubjectConfirmationData');
        $this->subjectConfirmation->SubjectConfirmationData = $this->subjectConfirmationData;
    }

    /**
     * @group assertion-validation
     * @test
     */
    public function timestamp_in_the_future_beyond_graceperiod_is_not_valid()
    {
        $this->subjectConfirmation->SubjectConfirmationData->NotBefore = $this->currentTime + 61;

        $validator = new SubjectConfirmationNotBefore();
        $result    = new Result();

        $validator->validate($this->subjectConfirmation, $result);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    /**
     * @group assertion-validation
     * @test
     */
    public function time_within_graceperiod_is_valid()
    {
        $this->subjectConfirmation->SubjectConfirmationData->NotBefore = $this->currentTime + 60;

        $validator = new SubjectConfirmationNotBefore();
        $result    = new Result();

        $validator->validate($this->subjectConfirmation, $result);

        $this->assertTrue($result->isValid());
    }

    /**
     * @group assertion-validation
     * @test
     */
    public function current_time_is_valid()
    {
        $this->subjectConfirmation->SubjectConfirmationData->NotBefore = $this->currentTime;

        $validator = new SubjectConfirmationNotBefore();
        $result    = new Result();

        $validator->validate($this->subjectConfirmation, $result);

        $this->assertTrue($result->isValid());
    }
}
