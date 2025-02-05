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

    it("can see detail of client on detail page", function () {
        $this->user = User::factory()->create([
            'role' => 'admin',
            'isActive' => true
        ]);

        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->get('/clients/' . $client->id);
        $response->assertStatus(200);
        $response->assertSee($client->client_id);
        $response->assertSee($client->username);
        $response->assertSee($client->address);
        $response->assertSee($client->package->name);
        $response->assertSee($client->bill_amount);
        $response->assertSee(ucfirst($client->status));
        $response->assertSee($client->billing_status ? 'Active' : 'Inactive');
        $response->assertSee($client->remarks);
    });
});


describe("Client Search", function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'role' => 'admin',
            'isActive' => true
        ]);

        $this->package = Package::factory()->create([
            'name' => 'Package 1',
            'price' => 100
        ]);

        $this->client1 = Client::factory()->create([
            'client_id' => "234234234",
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

        $this->client2 = Client::factory()->create([
            'client_id' => "34234234",
            'username' => 'Client 2',
            'phone_number' => '0987654321',
            'address' => 'Client 2 Address',
            'package_id' => $this->package->id,
            'bill_amount' => 200,
            'status' => 'due',
            'billing_status' => false,
            'remarks' => 'Client 2 Remarks',
            'created_by' => $this->user->id
        ]);
    });

    it('can search clients by client ID', function () {
        $response = $this->actingAs($this->user)->get('/clients?search=234234234');
        $response->assertStatus(200);

        $response->assertSee('Client 1');
        $response->assertDontSee('Client 2');
    });

    it('can search clients by username', function () {
        $response = $this->actingAs($this->user)->get('/clients?search=Client 1');
        $response->assertStatus(200);
        $response->assertSee('Client 1');
        $response->assertDontSee('Client 2');
    });

    it('can search clients by phone number', function () {
        $response = $this->actingAs($this->user)->get('/clients?search=1234567890');
        $response->assertStatus(200);
        $response->assertSee('Client 1');
        $response->assertDontSee('Client 2');
    });

    it('can search clients by partial information', function () {
        $response = $this->actingAs($this->user)->get('/clients?search=Client');
        $response->assertStatus(200);
        $response->assertSee('Client 1');
        $response->assertSee('Client 2');

        $response = $this->actingAs($this->user)->get('/clients?search=123');
        $response->assertStatus(200);
        $response->assertSee('Client 1');
        $response->assertDontSee('Client 2');
    });
});
