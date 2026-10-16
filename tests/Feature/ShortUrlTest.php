<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(
        string $role,
        ?Company $company = null
    ): User {
        return User::factory()->create([
            'role' => $role,
            'company_id' => $company?->id,
        ]);
    }

    public function test_admin_and_member_cannot_create_short_urls(): void
    {
        $company = Company::create(['name' => 'A']);

        $admin = $this->makeUser('admin', $company);
        $member = $this->makeUser('member', $company);

        $this->actingAs($admin)
            ->post('/short-urls', [
                'original_url' => 'http://127.0.0.1:8000',
            ])
            ->assertForbidden();

        $this->actingAs($member)
            ->post('/short-urls', [
                'original_url' => 'http://127.0.0.1:8000',
            ])
            ->assertForbidden();
    }

    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superAdmin = $this->makeUser('superadmin');

        $this->actingAs($superAdmin)
            ->post('/short-urls', [
                'original_url' => 'http://127.0.0.1:8000',
            ])
            ->assertForbidden();
    }

    public function test_admin_sees_only_short_urls_outside_their_company(): void
    {
        $companyA = Company::create(['name' => 'A']);
        $companyB = Company::create(['name' => 'B']);

        $adminA = $this->makeUser('admin', $companyA);
        $salesA = $this->makeUser('sales', $companyA);
        $salesB = $this->makeUser('sales', $companyB);

        ShortUrl::create([
            'company_id' => $companyA->id,
            'user_id' => $salesA->id,
            'original_url' => 'https://a.com',
            'short_code' => 'aaaaaa',
        ]);

        ShortUrl::create([
            'company_id' => $companyB->id,
            'user_id' => $salesB->id,
            'original_url' => 'https://b.com',
            'short_code' => 'bbbbbb',
        ]);

        $this->actingAs($adminA)
            ->get('/short-urls')
            ->assertOk()
            ->assertSee('bbbbbb')
            ->assertDontSee('aaaaaa');
    }

    public function test_member_sees_only_urls_not_created_by_themselves(): void
    {
        $company = Company::create(['name' => 'A']);

        $member1 = $this->makeUser('member', $company);
        $member2 = $this->makeUser('member', $company);

        ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member1->id,
            'original_url' => 'https://one.com',
            'short_code' => 'one111',
        ]);

        ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $member2->id,
            'original_url' => 'https://two.com',
            'short_code' => 'two222',
        ]);

        $this->actingAs($member1)
            ->get('/short-urls')
            ->assertOk()
            ->assertSee('two222')
            ->assertDontSee('one111');
    }

    public function test_short_url_redirect_requires_authentication(): void
    {
        $company = Company::create(['name' => 'A']);
        $sales = $this->makeUser('sales', $company);

        ShortUrl::create([
            'company_id' => $company->id,
            'user_id' => $sales->id,
            'original_url' => 'https://google.com',
            'short_code' => 'ggl111',
        ]);

        $this->get('/s/ggl111')
            ->assertRedirect('/login');

        $this->actingAs($sales)
            ->get('/s/ggl111')
            ->assertRedirect('https://google.com');
    }
}