<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Model\Response\Error\ValidationErrorResponse;
use App\Model\Response\Error\ViolationResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previous  = $exception->getPrevious();

        if (!$exception instanceof UnprocessableEntityHttpException ||
            !$previous instanceof ValidationFailedException
        ) {
            return;
        }

        $violations = [];
        foreach ($previous->getViolations() as $violation) {
            $value = $violation->getParameters()['{{ value }}'] ?? '';
            $value = trim($value, '"');

            $message = $violation->getMessage();

            if ($message instanceof \Stringable) {
                $message = $message->__toString();
            }

            $violations[] = new ViolationResponse(
                $violation->getPropertyPath(),
                $message,
                $value,
            );
        }

        $validationErrorResponse = new ValidationErrorResponse($violations);

        $event->setResponse(new JsonResponse($validationErrorResponse, 422));
    }
}
