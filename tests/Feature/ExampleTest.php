<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('admin cannot add a product with zero stock', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Makanan']);

    $response = $this->actingAs($admin)->from('/products/create')->post('/products', [
        'category_id' => $category->id,
        'code' => 'BRG001',
        'name' => 'Produk Uji',
        'unit' => 'pcs',
        'price' => 10000,
        'stock' => 0,
    ]);

    $response
        ->assertRedirect('/products/create')
        ->assertSessionHasErrors(['stock' => 'Stok awal harus lebih dari 0.']);
    $this->assertDatabaseMissing('products', ['code' => 'BRG001']);
});

test('admin cannot add a product with zero price', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Elektronik']);

    $response = $this->actingAs($admin)->from('/products/create')->post('/products', [
        'category_id' => $category->id,
        'code' => 'BRG003',
        'name' => 'Produk Harga Nol',
        'unit' => 'pcs',
        'price' => 0,
        'stock' => 5,
    ]);

    $response
        ->assertRedirect('/products/create')
        ->assertSessionHasErrors(['price' => 'Harga harus lebih dari 0.']);
    $this->assertDatabaseMissing('products', ['code' => 'BRG003']);
});

test('admin can add a product with positive stock', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create(['name' => 'Minuman']);

    $response = $this->actingAs($admin)->post('/products', [
        'category_id' => $category->id,
        'code' => 'BRG002',
        'name' => 'Produk Valid',
        'unit' => 'pcs',
        'price' => 5000,
        'stock' => 1,
    ]);

    $response
        ->assertRedirect('/products')
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('products', [
        'code' => 'BRG002',
        'stock' => 1,
    ]);
});
