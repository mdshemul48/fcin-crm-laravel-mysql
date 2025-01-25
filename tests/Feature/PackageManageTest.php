<?php

use App\Models\Package;
use App\Models\User;

describe('Package Manage', function () {
    describe("Admin user can manage packages", function () {
        beforeEach(function () {
            $this->user = User::factory()->create([
                'role' => 'admin',
                'isActive' => true
            ]);
        });
        it('can see packages page', function () {
            $response = $this->actingAs($this->user)->get('/packages');
            $response->assertStatus(200);
        });

        it("can create a package", function () {
            $response = $this->actingAs($this->user)->post('/packages', [
                'name' => 'Package 1',
                'price' => 100
            ]);
            $response->assertStatus(302);
            $this->assertDatabaseHas('packages', [
                'name' => 'Package 1',
                'price' => 100
            ]);
        });

        it('can see created packages on packages page', function () {
            $package1 = Package::factory()->create(['name' => 'Package 1', 'price' => 100]);
            $package2 = Package::factory()->create(['name' => 'Package 2', 'price' => 200]);
            $response = $this->actingAs($this->user)->get('/packages');
            $response->assertSee($package1->name);
            $response->assertSee($package1->price);
            $response->assertSee($package2->name);
            $response->assertSee($package2->price);
        });
    });

    describe("Support user can not manage packages", function () {
        beforeEach(function () {
            $this->user = User::factory()->create([
                'role' => 'support',
                'isActive' => true
            ]);
        });

        it('can not see packages page', function () {
            $this->user->role = 'support';
            $response = $this->actingAs($this->user)->get('/packages');
            $response->assertStatus(302);
        });

        it("can not create a package", function () {
            $response = $this->actingAs($this->user)->post('/packages', [
                'name' => 'Package 1',
                'price' => 100
            ]);
            $response->assertStatus(302);
            $response->assertRedirect('/');
            $this->assertDatabaseMissing('packages', [
                'name' => 'Package 1',
                'price' => 100
            ]);
        });
    });
});
