<?php

use App\Models\Client;
use App\Models\Package;
use App\Models\User;

describe("Client Manage", function () {
    describe("Admin user can manage clients", function () {
        beforeEach(function () {
            $this->user = User::factory()->create([
                'role' => 'admin',
                'isActive' => true
            ]);

            $this->package = Package::factory()->create([
                'name' => 'Package 1',
                'price' => 100
            ]);
        });

        it('can see clients page', function () {
            $response = $this->actingAs($this->user)->get('/clients');
            $response->assertStatus(200);
        });

        it("can create a client", function () {
            $response = $this->actingAs($this->user)->post('/clients', [
                'client_id' => 1,
                'username' => 'Client 1',
                'phone_number' => '1234567890',
                'address' => 'Client 1 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 150,
                'status' => 'paid',
                'billing_status' => true,
                'remarks' => 'Client 1 Remarks',
                'created_by' => $this->user->id
            ]);
            $response->assertStatus(302);
            $this->assertDatabaseHas('clients', [
                'client_id' => 1,
                'username' => 'Client 1',
                'phone_number' => '1234567890',
                'address' => 'Client 1 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 150,
                'status' => 'paid',
                'billing_status' => true,
                'remarks' => 'Client 1 Remarks',
                'created_by' => $this->user->id
            ]);
        });

        it("can edit a client", function () {
            $client = Client::factory()->create([
                'client_id' => 1,
                'username' => 'Client 1',
                'phone_number' => '1234567890',
                'address' => 'Client 1 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 150,
                'billing_status' => true,
                'remarks' => 'Client 1 Remarks',
                'created_by' => $this->user->id
            ]);

            $response = $this->actingAs($this->user)->put('/clients/' . $client->id, [
                'client_id' => 1,
                'username' => 'Client 2',
                'phone_number' => '1234567890',
                'address' => 'Client 2 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 200,
                'billing_status' => false,
                'remarks' => 'Client 2 Remarks',
                'created_by' => $this->user->id
            ]);
            $response->assertStatus(302);
            $this->assertDatabaseHas('clients', [
                'client_id' => 1,
                'username' => 'Client 2',
                'phone_number' => '1234567890',
                'address' => 'Client 2 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 200,
                'billing_status' => false,
                'remarks' => 'Client 2 Remarks',
                'created_by' => $this->user->id
            ]);
        });
    });

    describe("Support user can manage clients", function () {
        beforeEach(function () {
            $this->user = User::factory()->create([
                'role' => 'support',
                'isActive' => true
            ]);

            $this->package = Package::factory()->create([
                'name' => 'Package 1',
                'price' => 100
            ]);
        });

        it('can see clients page', function () {
            $response = $this->actingAs($this->user)->get('/clients');
            $response->assertStatus(200);
        });

        it("can create a client", function () {
            $response = $this->actingAs($this->user)->post('/clients', [
                'client_id' => 1,
                'username' => 'Client 1',
                'phone_number' => '1234567890',
                'address' => 'Client 1 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 150,
                'status' => 'paid',
                'billing_status' => true,
                'remarks' => 'Client 1 Remarks',
                'created_by' => $this->user->id
            ]);
            $response->assertStatus(302);
            $this->assertDatabaseHas('clients', [
                'client_id' => 1,
                'username' => 'Client 1',
                'phone_number' => '1234567890',
                'address' => 'Client 1 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 150,
                'status' => 'paid',
                'billing_status' => true,
                'remarks' => 'Client 1 Remarks',
                'created_by' => $this->user->id
            ]);
        });

        it("can edit a client", function () {
            $client = Client::factory()->create([
                'client_id' => 1,
                'username' => 'Client 1',
                'phone_number' => '1234567890',
                'address' => 'Client 1 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 150,
                'billing_status' => true,
                'remarks' => 'Client 1 Remarks',
                'created_by' => $this->user->id
            ]);

            $response = $this->actingAs($this->user)->put('/clients/' . $client->id, [
                'client_id' => 1,
                'username' => 'Client 2',
                'phone_number' => '1234567890',
                'address' => 'Client 2 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 200,
                'billing_status' => false,
                'remarks' => 'Client 2 Remarks',
                'created_by' => $this->user->id
            ]);
            $response->assertStatus(302);
            $this->assertDatabaseHas('clients', [
                'client_id' => 1,
                'username' => 'Client 2',
                'phone_number' => '1234567890',
                'address' => 'Client 2 Address',
                'package_id' => $this->package->id,
                'bill_amount' => 200,
                'billing_status' => false,
                'remarks' => 'Client 2 Remarks',
                'created_by' => $this->user->id
            ]);
        });
    });
});
