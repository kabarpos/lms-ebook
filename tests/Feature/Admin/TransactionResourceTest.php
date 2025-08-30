<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\TransactionResource;
use App\Models\Category;
use App\Models\Course;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TransactionResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $customer;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role and user
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
        
        // Create test customer and course
        $this->customer = User::factory()->create();
        $category = Category::factory()->create();
        $this->course = Course::factory()->create(['category_id' => $category->id]);
        
        $this->actingAs($this->adminUser);
    }

    public function test_can_render_transaction_resource_list_page(): void
    {
        // Arrange
        Transaction::factory()->count(5)->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\ListTransactions::class)
            ->assertSuccessful();
    }

    public function test_can_list_transactions(): void
    {
        // Arrange
        $transactions = Transaction::factory()->count(3)->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\ListTransactions::class)
            ->assertCanSeeTableRecords($transactions);
    }

    public function test_can_render_transaction_resource_create_page(): void
    {
        // Act & Assert
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->assertSuccessful();
    }

    public function test_can_create_transaction_step_1_product_selection(): void
    {
        // Arrange
        $transactionData = [
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
        ];
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm($transactionData)
            ->call('nextStep')
            ->assertHasNoFormErrors()
            ->assertSet('currentStep', 2);
    }

    public function test_can_create_transaction_step_2_pricing_calculation(): void
    {
        // Arrange
        $course = Course::factory()->create([
            'price' => 299000,
            'category_id' => Category::factory()->create()->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm([
                'user_id' => $this->customer->id,
                'course_id' => $course->id,
            ])
            ->call('nextStep')
            ->assertFormSet([
                'subtotal' => 299000,
                'tax_amount' => 29900, // 10% tax
                'total_amount' => 328900,
            ]);
    }

    public function test_can_complete_transaction_creation(): void
    {
        // Arrange
        $transactionData = [
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'subtotal' => $this->course->price,
            'tax_amount' => $this->course->price * 0.1,
            'total_amount' => $this->course->price * 1.1,
            'payment_status' => 'pending',
            'payment_method' => 'bank_transfer',
        ];
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm($transactionData)
            ->call('create')
            ->assertHasNoFormErrors();
            
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'payment_status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);
    }

    public function test_can_validate_transaction_creation(): void
    {
        // Act & Assert
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm([
                'user_id' => null,
                'course_id' => null,
                'subtotal' => -100,
                'total_amount' => -100,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'user_id' => 'required',
                'course_id' => 'required',
                'subtotal' => 'min',
                'total_amount' => 'min',
            ]);
    }

    public function test_can_render_transaction_resource_edit_page(): void
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\EditTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->assertSuccessful();
    }

    public function test_can_retrieve_transaction_data_for_editing(): void
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'subtotal' => 299000,
            'tax_amount' => 29900,
            'total_amount' => 328900,
            'payment_status' => 'pending',
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\EditTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->assertFormSet([
                'user_id' => $this->customer->id,
                'course_id' => $this->course->id,
                'subtotal' => 299000,
                'tax_amount' => 29900,
                'total_amount' => 328900,
                'payment_status' => 'pending',
            ]);
    }

    public function test_can_save_transaction_changes(): void
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'payment_status' => 'pending',
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\EditTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->fillForm([
                'payment_status' => 'completed',
                'payment_method' => 'credit_card',
            ])
            ->call('save')
            ->assertHasNoFormErrors();
            
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'payment_status' => 'completed',
            'payment_method' => 'credit_card',
        ]);
    }

    public function test_can_delete_transaction(): void
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\EditTransaction::class, [
            'record' => $transaction->getRouteKey(),
        ])
            ->callAction(DeleteAction::class)
            ->assertSuccessful();
            
        $this->assertModelMissing($transaction);
    }

    public function test_can_search_transactions(): void
    {
        // Arrange
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);
        
        $transaction1 = Transaction::factory()->create([
            'user_id' => $user1->id,
            'course_id' => $this->course->id,
        ]);
        $transaction2 = Transaction::factory()->create([
            'user_id' => $user2->id,
            'course_id' => $this->course->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\ListTransactions::class)
            ->searchTable('John')
            ->assertCanSeeTableRecords([$transaction1])
            ->assertCanNotSeeTableRecords([$transaction2]);
    }

    public function test_can_filter_transactions_by_payment_status(): void
    {
        // Arrange
        $pendingTransaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'payment_status' => 'pending',
        ]);
        $completedTransaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'payment_status' => 'completed',
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\ListTransactions::class)
            ->filterTable('payment_status', 'pending')
            ->assertCanSeeTableRecords([$pendingTransaction])
            ->assertCanNotSeeTableRecords([$completedTransaction]);
    }

    public function test_can_filter_transactions_by_payment_method(): void
    {
        // Arrange
        $bankTransferTransaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'payment_method' => 'bank_transfer',
        ]);
        $creditCardTransaction = Transaction::factory()->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
            'payment_method' => 'credit_card',
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\ListTransactions::class)
            ->filterTable('payment_method', 'bank_transfer')
            ->assertCanSeeTableRecords([$bankTransferTransaction])
            ->assertCanNotSeeTableRecords([$creditCardTransaction]);
    }

    public function test_can_bulk_delete_transactions(): void
    {
        // Arrange
        $transactions = Transaction::factory()->count(3)->create([
            'user_id' => $this->customer->id,
            'course_id' => $this->course->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\ListTransactions::class)
            ->selectTableRecords($transactions)
            ->callTableBulkAction(DeleteBulkAction::class)
            ->assertSuccessful();
            
        foreach ($transactions as $transaction) {
            $this->assertModelMissing($transaction);
        }
    }

    public function test_wizard_navigation_works_correctly(): void
    {
        // Act & Assert - Test forward navigation
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm([
                'user_id' => $this->customer->id,
                'course_id' => $this->course->id,
            ])
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->call('previousStep')
            ->assertSet('currentStep', 1);
    }

    public function test_transaction_totals_calculation_is_accurate(): void
    {
        // Arrange
        $course = Course::factory()->create([
            'price' => 500000,
            'category_id' => Category::factory()->create()->id,
        ]);
        
        // Act & Assert
        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm([
                'user_id' => $this->customer->id,
                'course_id' => $course->id,
            ])
            ->call('nextStep')
            ->assertFormSet([
                'subtotal' => 500000,
                'tax_amount' => 50000, // 10% tax
                'total_amount' => 550000,
            ]);
    }
}