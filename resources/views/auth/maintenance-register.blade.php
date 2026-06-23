<form action="{{ route('maintenance.register') }}" method="POST">
    @csrf

    <div>
        <label>Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>

    <div>
        <label>Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
    </div>

    <div>
        <label>Phone Number</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required>
    </div>

    <div>
        <label>Governorate</label>
        <select name="governorate" required>
            <option value="">-- Select Governorate --</option>
            @foreach($governorates as $slug => $label)
            <option value="{{ $slug }}" {{ old('governorate') == $slug ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Address / Location (العنوان بالتفصيل)</label>
        <input type="text" name="location" value="{{ old('location') }}">
    </div>

    <div>
        <label>Maintenance Specialty</label>
        <select name="maintenance_type" required>
            <option value="">-- Select Specialty --</option>
            @foreach($maintenanceTypes as $slug => $label)
            <option value="{{ $slug }}" {{ old('maintenance_type') == $slug ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit">Register as Maintenance</button>
</form>