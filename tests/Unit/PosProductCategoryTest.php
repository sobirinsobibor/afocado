<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Pos\Models\PosProduct;
use Tests\TestCase;

class PosProductCategoryTest extends TestCase
{
    public function test_product_has_category_relationship_and_fillable_field(): void
    {
        $product = new PosProduct();

        $this->assertTrue(method_exists($product, 'category'));
        $this->assertInstanceOf(BelongsTo::class, $product->category());
        $this->assertContains('pos_product_category_id', $product->getFillable());
    }
}
