<?php
declare(strict_types=1);

namespace App\Tests\EventListener;

use App\EventListener\ValidationExceptionListener;
use App\Model\Response\Error\ValidationErrorResponse;
use App\Model\Response\Error\ViolationResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[CoversClass(ValidationExceptionListener::class)]
#[CoversClass(ValidationErrorResponse::class)]
#[CoversClass(ViolationResponse::class)]
class ValidationExceptionListenerTest extends TestCase
{
    use ProphecyTrait;

    private ValidationExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ValidationExceptionListener();
    }

    public function testOnKernelException(): void
    {
        /** @var ObjectProphecy<HttpKernelInterface> $httpKernelInterface */
        $httpKernelInterface = $this->prophesize(HttpKernelInterface::class);

        $constraintViolation1 = new ConstraintViolation(
            'Please submit a valid email',
            'Please submit a valid email',
            ['{{ value }}' => '"this-is-not-an-email.nl"'],
            '',
            'email',
            '',
        );

        $constraintViolation2 = new ConstraintViolation(
            'Please make sure the password is at least 10 characters',
            'Please make sure the password is at least 10 characters',
            ['{{ value }}' => 'tooshort'],
            '',
            'password',
            '',
        );

        $stringable = $this->prophesize(\Stringable::class);
        $stringable->__toString()->willReturn('a stringable message');

        $constraintViolation3 = new ConstraintViolation(
            $stringable->reveal(),
            'a stringable message',
            [],
            '',
            'stringable',
            '',
        );

        $exceptionEvent = new ExceptionEvent(
            $httpKernelInterface->reveal(),
            new Request(),
            1,
            new UnprocessableEntityHttpException(
                'I am a UnprocessableEntityHttpException',
                previous: new ValidationFailedException(
                    'I am a ValidationFailedException',
                    new ConstraintViolationList([
                        $constraintViolation1,
                        $constraintViolation2,
                        $constraintViolation3,
                    ])
                )
            )
        );

        $response = new JsonResponse('This is a random response');

        $exceptionEvent->setResponse($response);

        $this->listener->onKernelException($exceptionEvent);

        $violation1 = new ViolationResponse(
            'email',
            'Please submit a valid email',
            'this-is-not-an-email.nl'
        );

        $violation2 = new ViolationResponse(
            'password',
            'Please make sure the password is at least 10 characters',
            'tooshort'
        );

        $violation2 = new ViolationResponse(
            'stringable',
            'a stringable message',
            ''
        );

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(new JsonResponse(
            new ValidationErrorResponse([$violation1, $violation2]),
            422
        ));

        /** @var string $actualResponse */
        $actualResponse = json_encode($exceptionEvent->getResponse());

        self::assertJsonStringEqualsJsonString($expectedResponse, $actualResponse);
    }

    public function testOnKernelExceptionWithIrrelevantException(): void
    {
        /** @var ObjectProphecy<HttpKernelInterface> $httpKernelInterface */
        $httpKernelInterface = $this->prophesize(HttpKernelInterface::class);

        $exceptionEvent = new ExceptionEvent(
            $httpKernelInterface->reveal(),
            new Request(),
            1,
            new \Exception(
                'I am not a UnprocessableEntityHttpException',
                previous: new ValidationFailedException(
                    'I am not ValidationFailedException',
                    new ConstraintViolationList([])
                )
            )
        );

        $response = new JsonResponse('This is a random response');

        $exceptionEvent->setResponse($response);

        $this->listener->onKernelException($exceptionEvent);

        self::assertSame($response, $exceptionEvent->getResponse());
    }

    public function testOnKernelExceptionWithIrrelevantPreviousException(): void
    {
        /** @var ObjectProphecy<HttpKernelInterface> $httpKernelInterface */
        $httpKernelInterface = $this->prophesize(HttpKernelInterface::class);

        $exceptionEvent = new ExceptionEvent(
            $httpKernelInterface->reveal(),
            new Request(),
            1,
            new UnprocessableEntityHttpException(
                'I am a UnprocessableEntityHttpException',
                previous: new \Exception('I am not a ValidationFailedException')
            )
        );

        $response = new JsonResponse('This is a random response');

        $exceptionEvent->setResponse($response);

        $this->listener->onKernelException($exceptionEvent);

        self::assertSame($response, $exceptionEvent->getResponse());
    }
}
