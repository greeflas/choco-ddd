<?php


namespace Billing\Tests\Domain\Aggregate;

use Billing\Domain\Aggregate\Customer;
use Billing\Domain\Aggregate\Merchant;
use Billing\Domain\DTO\Customer\CustomerRegistrationDto;
use Billing\Domain\DTO\Merchant\MerchantRegistrationDto;
use Billing\Domain\Event\MerchantWasRegistered;
use Billing\Tests\Integration\TestCase;
use Ramsey\Uuid\Uuid;

class MerchantTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $dto = MerchantRegistrationDto::fromArray([
            'id' => Uuid::uuid4(),
            'name' => 'Foo Bar'
        ]);
        $merchant = Merchant::register($dto);

        $this->assertEquals($dto->id, $merchant->id());
        $this->assertSame($dto->name, $merchant->name());

        $events = $merchant->flushEvents();
        $found = false;
        foreach ($events as $event) {
            if ($event instanceof MerchantWasRegistered) {
                $found = true;
                $this->assertTrue($merchant->id()->equals($event->merchantId()));
            }
        }
        $this->assertTrue($found, 'Event MerchantWasRegistered was not found in Merchant objecct');
    }
}
