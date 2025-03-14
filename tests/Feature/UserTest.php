<?php

use App\Models\User;

describe("Admin User", function () {
    beforeEach(function () {
        $this->password = 'password';
        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            "password" => $this->password,
            "role" => "admin",
            'isActive' => true
        ]);
    });

    it('can can login and see dashboard', function () {

        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => $this->password
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->get("/")->assertSee("logout");
        $this->assertAuthenticatedAs($this->user);
    });

    it("can create another user for support as admin", function () {
        $testUser = User::factory()->raw(["password" => "password", 'role' => "support", "isActive" => true]);
        $response = $this->actingAs($this->user)->post("/users", $testUser);

        $response->assertStatus(302);
        $response->assertRedirect("/users");

        $this->assertDatabaseHas("users", ["email" => $testUser["email"]]);

        $userPage = $this->get("/users");
        $userPage->assertSee($testUser["name"]);
        $userPage->assertSee($testUser["email"]);
    });

    afterEach(function () {
        $this->user->delete();
    });
});


describe("Support User", function () {
    beforeEach(function () {
        $this->password = 'password';
        $this->user = User::factory()->create(["password" => "password", 'role' => "support", "isActive" => true]);
    });

    it('can see dashboard as an support user', function () {
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    });

    it("can not see the user list on profile menu", function () {
        $response = $this->actingAs($this->user)->get("/");
        $response->assertDontSee("Users List");
    });

    it("can not see the users page", function () {
        $response = $this->actingAs($this->user)->get("/users");
        $response->assertStatus(302);
        $response->assertRedirect("/");
    });

    it('can not create another user as support', function () {
        $testUser = User::factory()->raw(["password" => "password", 'role' => "support"]);
        $response = $this->actingAs($this->user)->post("/users", $testUser);

        $response->assertStatus(302);
        $response->assertRedirect("/");
        $this->assertDatabaseMissing("users", ["email" => $testUser["email"]]);
    });
});


describe("Disabled User", function () {
    beforeEach(function () {
        $this->password = 'password';
        $this->user = User::factory()->create(["password" => "password", 'role' => "support", "isActive" => 0]);
    });

    it('can not login as disabled user', function () {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => $this->password
        ]);
        $response->assertStatus(302);
        $response->assertRedirect("/login");
    });

    it('can redirect disabled user to login page while using software', function () {
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    });

    afterEach(function () {
        $this->user->delete();
    });
});
