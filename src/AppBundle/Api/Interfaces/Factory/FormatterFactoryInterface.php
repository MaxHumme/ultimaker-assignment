<?php
namespace AppBundle\Api\Interfaces\Factory;

use AppBundle\Api\Formatter\PrintFormatter;
use AppBundle\Api\Formatter\PrintsFormatter;
use AppBundle\Domain\Entity\ThreeDPrint;

/**
 * Interface FormatterFactoryInterface
 *
 * Responsible for creating formatters.
 *
 * @author Max Humme <max@humme.nl>
 */
interface FormatterFactoryInterface
{
    /**
     * Returns the PrintFormatter.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint $print
     * @return \AppBundle\Api\Formatter\PrintFormatter
     */
    public function createPrintFormatter(ThreeDPrint $print): PrintFormatter;

    /**
     * Returns the PrintsFormatter.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint[] $prints
     * @return \AppBundle\Api\Formatter\PrintsFormatter
     */
    public function createPrintsFormatter(array $prints): PrintsFormatter;
}
