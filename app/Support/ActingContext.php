<?php

namespace App\Support;

class ActingContext
{
    protected ?int $onBehalfOf = null;
    protected ?int $delegationId = null;

    public function onBehalfOf(): ?int
    {
        return $this->onBehalfOf;
    }

    public function delegationId(): ?int
    {
        return $this->delegationId;
    }

    public function set(?int $onBehalfOf, ?int $delegationId = null): void
    {
        $this->onBehalfOf = $onBehalfOf;
        $this->delegationId = $delegationId;
    }

    public function clear(): void
    {
        $this->onBehalfOf = null;
        $this->delegationId = null;
    }

    /**
     * Run a callback as a delegate acting on someone's behalf. Any audit
     * records written inside the callback carry the delegation context.
     */
    public function act(int $onBehalfOf, ?int $delegationId, callable $callback): mixed
    {
        $prevBehalf = $this->onBehalfOf;
        $prevDelegation = $this->delegationId;

        $this->set($onBehalfOf, $delegationId);

        try {
            return $callback();
        } finally {
            $this->onBehalfOf = $prevBehalf;
            $this->delegationId = $prevDelegation;
        }
    }
}
