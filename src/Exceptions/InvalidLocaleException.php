<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2019 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);

namespace DMX\Application\Intl\Exceptions;

class InvalidLocaleException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    protected const DEFAULT_MESSAGE = 'The locale functionality is not implemented on this environment or the specified locale does not exist.';

    /**
     * {@inheritdoc}
     */
    public function __construct(?string $message = null, int $code = 0, \Throwable $previous = null)
    {
        if (empty($message)) {
            $message = self::DEFAULT_MESSAGE;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string|null $message
     *
     * @return InvalidLocaleException
     */
    public static function becauseLocaleDoesNotExist(?string $message = null): InvalidLocaleException
    {
        if (empty($message)) {
            $message = self::DEFAULT_MESSAGE;
        }

        return new static($message);
    }
}
