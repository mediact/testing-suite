<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\TestingSuite\Composer\GrumPhp;

use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\TaskInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait OptionalTaskTrait
{
    /** @var TaskInterface */
    private $innerTask;

    /**
     * Whether the inner task is supported in the current environment.
     *
     * @return bool
     */
    abstract protected function supportsInnerTask(): bool;

    /**
     * Create the inner task.
     *
     * @return TaskInterface
     */
    abstract protected function createInnerTask(): TaskInterface;

    /**
     * Get the inner task.
     *
     * @return TaskInterface
     */
    protected function getInnerTask(): TaskInterface
    {
        if ($this->innerTask === null) {
            $this->innerTask = $this->supportsInnerTask()
                ? $this->createInnerTask()
                : new NullTask();
        }

        return $this->innerTask;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getInnerTask()->getName();
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->getInnerTask()->getConfiguration();
    }

    /**
     * @return OptionsResolver
     */
    public function getConfigurableOptions(): OptionsResolver
    {
        return $this->getInnerTask()->getConfigurableOptions();
    }

    /**
     * This methods specifies if a task can run in a specific context.
     *
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(ContextInterface $context): bool
    {
        return $this->getInnerTask()->canRunInContext($context);
    }

    /**
     * @param ContextInterface $context
     *
     * @return TaskResultInterface
     */
    public function run(ContextInterface $context): TaskResultInterface
    {
        return $this->getInnerTask()->run($context);
    }
}
