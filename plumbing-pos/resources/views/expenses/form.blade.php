
<form method="POST" action="{{ $action }}" style="max-width:650px;">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-bottom:16px;">
        <label for="title">Expense Title</label>
        <input type="text" id="title" name="title"
               value="{{ old('title', $expense?->title) }}"
               maxlength="255" required style="width:100%;">
    </div>

    <div style="margin-bottom:16px;">
        <label for="category">Category</label>
        @php
            $selectedCategory = old('category', $expense?->category);
            $categories = [
                'Rent', 'Electricity', 'Water', 'Transport',
                'Salaries', 'Repairs and Maintenance',
                'Internet', 'Packaging', 'Other'
            ];
        @endphp

        <select id="category" name="category" required style="width:100%;">
            <option value="">Select Category</option>

            @foreach($categories as $category)
                <option value="{{ $category }}"
                    @selected($selectedCategory === $category)>
                    {{ $category }}
                </option>
            @endforeach

            @if($selectedCategory && !in_array($selectedCategory, $categories))
                <option value="{{ $selectedCategory }}" selected>
                    {{ $selectedCategory }}
                </option>
            @endif
        </select>
    </div>

    <div style="margin-bottom:16px;">
        <label for="amount">Amount (KSh)</label>
        <input type="number" id="amount" name="amount"
               value="{{ old('amount', $expense?->amount) }}"
               min="0.01" step="0.01" required style="width:100%;">
    </div>

    <div style="margin-bottom:16px;">
        <label for="expense_date">Expense Date</label>
        <input type="date" id="expense_date" name="expense_date"
               value="{{ old('expense_date', $expense?->expense_date?->format('Y-m-d') ?? now()->toDateString()) }}"
               required style="width:100%;">
    </div>

    <div style="margin-bottom:16px;">
        <label for="payment_method">Payment Method</label>

        @php
            $selectedPayment = old(
                'payment_method',
                $expense?->payment_method
            );
        @endphp

        <select id="payment_method" name="payment_method"
                required style="width:100%;">
            <option value="">Select Payment Method</option>

            @foreach(['Cash', 'M-Pesa', 'Bank Transfer', 'Card', 'Other'] as $payment)
                <option value="{{ $payment }}"
                    @selected($selectedPayment === $payment)>
                    {{ $payment }}
                </option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom:16px;">
        <label for="description">Description (Optional)</label>
        <textarea id="description" name="description"
                  maxlength="2000" rows="4"
                  style="width:100%;">{{ old('description', $expense?->description) }}</textarea>
    </div>

    <button type="submit" class="btn">Save Expense</button>

    <a href="{{ route('expenses.index') }}" class="btn-secondary">
        Cancel
    </a>
</form>