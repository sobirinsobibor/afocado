<?php

namespace Tests\Unit;

use Modules\Pos\Models\PosProduct;
use Modules\Pos\Models\PosUnit;
use Tests\TestCase;

class PosUnitTest extends TestCase
{
    public function test_pos_unit_model_has_expected_fillable_fields(): void
    {
        $unit = new PosUnit();

        $this->assertContains('name', $unit->getFillable());
        $this->assertContains('slug', $unit->getFillable());
        $this->assertContains('type', $unit->getFillable());
    }

    public function test_product_has_price_and_stock_unit_relationships(): void
    {
        $product = new PosProduct();

        $this->assertTrue(method_exists($product, 'stockUnit'));
        $this->assertTrue(method_exists($product, 'priceUnit'));
    }
}
