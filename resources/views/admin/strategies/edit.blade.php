@extends('layout.admin')

@section('content')
<style>
    :root {
        --primary-green: #9EDD05;
        --dark-green: #0C3A30;
        --accent-green: #8AC304;
    }

    .form-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(158, 221, 5, 0.1);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-green), var(--accent-green));
        color: var(--dark-green);
        font-weight: 700;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(158, 221, 5, 0.3);
    }

    .feature-item, .module-item, .objective-item, .prerequisite-item {
        background: #f9fafb;
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border: 1px solid #e5e7eb;
    }

    .remove-btn {
        color: #ef4444;
        cursor: pointer;
        font-size: 0.75rem;
    }

    .add-btn {
        background: var(--primary-green);
        color: var(--dark-green);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        margin-top: 0.5rem;
    }
</style>

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <div>
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Edit Course</h5>
            <p class="text-sm text-gray-500 mt-1">Update "{{ $strategy->name }}"</p>
        </div>
        <ul class="flex items-center gap-[6px]">
            <li><a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;">
                <iconify-icon icon="solar:home-smile-angle-outline"></iconify-icon> Dashboard</a>
            </li>
            <li>-</li>
            <li><a href="{{ route('admin.strategies.strategyindex') }}" class="hover:text-[#9EDD05]" style="color:#0C3A30;">Courses</a></li>
            <li>-</li>
            <li class="font-medium" style="color:#9EDD05;">Edit</li>
        </ul>
    </div>

    <form action="{{ route('admin.strategies.strategyupdate', $strategy->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Course Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input" required value="{{ old('name', $strategy->name) }}">
                </div>
                <div>
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-input" placeholder="auto-generated" value="{{ old('slug', $strategy->slug) }}">
                </div>
                <div>
                    <label class="form-label">Price (USD) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" step="0.01" class="form-input" required value="{{ old('price', $strategy->price) }}">
                </div>
                <div>
                    <label class="form-label">Duration (Days)</label>
                    <input type="number" name="duration_days" class="form-input" placeholder="Leave empty for lifetime" value="{{ old('duration_days', $strategy->duration_days) }}">
                </div>
                <div>
                    <label class="form-label">Difficulty Level</label>
                    <select name="difficulty_level" class="form-select">
                        <option value="">Select Difficulty</option>
                        <option value="beginner" {{ $strategy->difficulty_level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ $strategy->difficulty_level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ $strategy->difficulty_level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        <option value="expert" {{ $strategy->difficulty_level == 'expert' ? 'selected' : '' }}>Expert</option>
                    </select>
                </div>
             <div>
    <label class="form-label">Estimated Hours</label>
    <input type="text" name="estimated_hours" class="form-input" placeholder="e.g., 10 Hours, Self Paced, Unlimited Access" value="{{ old('estimated_hours', $strategy->estimated_hours ?? '') }}">
    <p class="text-xs text-gray-400 mt-1">Examples: "10 Hours", "Self Paced", "Unlimited Access", "8 Weeks"</p>
</div>
                <div>
                    <label class="form-label">Instructor Name</label>
                    <input type="text" name="instructor_name" class="form-input" value="{{ old('instructor_name', $strategy->instructor_name) }}">
                </div>
                <div>
                    <label class="form-label">Badge Text</label>
                    <input type="text" name="badge_text" class="form-input" placeholder="e.g., MOST POPULAR" value="{{ old('badge_text', $strategy->badge_text) }}">
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ $strategy->is_active ? 'checked' : '' }}> 
                        <span class="text-sm">Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_popular" value="1" {{ $strategy->is_popular ? 'checked' : '' }}> 
                        <span class="text-sm">Mark as Popular</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Descriptions -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Descriptions</h3>
            <div class="mb-4">
                <label class="form-label">Short Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" class="form-textarea" required>{{ old('description', $strategy->description) }}</textarea>
            </div>
            <div>
                <label class="form-label">Long Description</label>
                <textarea name="long_description" rows="6" class="form-textarea">{{ old('long_description', $strategy->long_description) }}</textarea>
            </div>
        </div>

        <!-- Images -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Images</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Cover Image</label>
                    @if($strategy->cover_image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($strategy->cover_image) }}" class="w-11 h-11 object-cover rounded-lg">
                    </div>
                    @endif
                    <input type="file" name="cover_image" class="form-input" accept="image/*">
                </div>
                <div>
                    <label class="form-label">Instructor Image</label>
                    @if($strategy->instructor_image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($strategy->instructor_image) }}" class="w-11 h-11 rounded-full object-cover">
                    </div>
                    @endif
                    <input type="file" name="instructor_image" class="form-input" accept="image/*">
                </div>
            </div>
        </div>

        <!-- Instructor Bio -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Instructor Information</h3>
            <div>
                <label class="form-label">Instructor Bio</label>
                <textarea name="instructor_bio" rows="4" class="form-textarea">{{ old('instructor_bio', $strategy->instructor_bio) }}</textarea>
            </div>
        </div>
<!-- Features -->
<div class="form-card">
    <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Course Features</h3>

    <div id="features-container">
        @php
            $features = is_array($strategy->features)
                ? $strategy->features
                : json_decode($strategy->features, true);
        @endphp

        @forelse($features ?? [] as $feature)
        <div class="feature-item flex items-center gap-2">
            <input type="text" name="features[]" class="form-input flex-1"
                value="{{ $feature }}">
            <button type="button" class="remove-feature text-red-500">Remove</button>
        </div>
        @empty
        <div class="feature-item flex items-center gap-2">
            <input type="text" name="features[]" class="form-input flex-1">
            <button type="button" class="remove-feature text-red-500">Remove</button>
        </div>
        @endforelse
    </div>

    <button type="button" id="add-feature" class="add-btn">+ Add Feature</button>
</div>

<!-- Learning Objectives -->
<div class="form-card">
    <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Learning Objectives</h3>

    <div id="objectives-container">
        @php
            $objectives = is_array($strategy->learning_objectives)
                ? $strategy->learning_objectives
                : json_decode($strategy->learning_objectives, true);
        @endphp

        @forelse($objectives ?? [] as $objective)
        <div class="feature-item flex items-center gap-2">
            <input type="text" name="learning_objectives[]" class="form-input flex-1"
                value="{{ $objective }}">
            <button type="button" class="remove-objective text-red-500">Remove</button>
        </div>
        @empty
        <div class="feature-item flex items-center gap-2">
            <input type="text" name="learning_objectives[]" class="form-input flex-1">
            <button type="button" class="remove-objective text-red-500">Remove</button>
        </div>
        @endforelse
    </div>

    <button type="button" id="add-objective" class="add-btn">+ Add Learning Objective</button>
</div>

<!-- Prerequisites -->
<div class="form-card">
    <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Prerequisites</h3>

    <div id="prerequisites-container">
        @php
            $prerequisites = is_array($strategy->prerequisites)
                ? $strategy->prerequisites
                : json_decode($strategy->prerequisites, true);
        @endphp

        @forelse($prerequisites ?? [] as $prerequisite)
        <div class="feature-item flex items-center gap-2">
            <input type="text" name="prerequisites[]" class="form-input flex-1"
                value="{{ $prerequisite }}">
            <button type="button" class="remove-prerequisite text-red-500">Remove</button>
        </div>
        @empty
        <div class="feature-item flex items-center gap-2">
            <input type="text" name="prerequisites[]" class="form-input flex-1">
            <button type="button" class="remove-prerequisite text-red-500">Remove</button>
        </div>
        @endforelse
    </div>

    <button type="button" id="add-prerequisite" class="add-btn">+ Add Prerequisite</button>
</div>

<!-- Modules -->
<div class="form-card">
    <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Course Modules</h3>

    <div id="modules-container">

        @php
            $modules = is_array($strategy->modules)
                ? $strategy->modules
                : json_decode($strategy->modules, true);
        @endphp

        @forelse($modules ?? [] as $index => $module)
        <div class="module-item">
            <div class="mb-2">
                <label class="form-label text-sm">Module Title</label>
                <input type="text"
                    name="modules[{{ $index }}][title]"
                    class="form-input"
                    value="{{ $module['title'] ?? '' }}">
            </div>

            <div class="mb-2">
                <label class="form-label text-sm">Module Content</label>
                <textarea
                    name="modules[{{ $index }}][content]"
                    rows="3"
                    class="form-textarea">{{ $module['content'] ?? '' }}</textarea>
            </div>

            <div>
                <label class="form-label text-sm">Video URL</label>
                <input type="url"
                    name="modules[{{ $index }}][video_url]"
                    class="form-input"
                    value="{{ $module['video_url'] ?? '' }}">
            </div>

            <button type="button" class="remove-module text-red-500 text-sm mt-2">
                Remove Module
            </button>
        </div>
        @empty
        <div class="module-item">
            <div class="mb-2">
                <label class="form-label text-sm">Module Title</label>
                <input type="text" name="modules[0][title]" class="form-input">
            </div>

            <div class="mb-2">
                <label class="form-label text-sm">Module Content</label>
                <textarea name="modules[0][content]" rows="3" class="form-textarea"></textarea>
            </div>

            <div>
                <label class="form-label text-sm">Video URL</label>
                <input type="url" name="modules[0][video_url]" class="form-input">
            </div>

            <button type="button" class="remove-module text-red-500 text-sm mt-2">
                Remove Module
            </button>
        </div>
        @endforelse

    </div>

    <button type="button" id="add-module" class="add-btn">+ Add Module</button>
</div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.strategies.strategyindex') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="btn-submit">Update Course</button>
        </div>
    </form>
</div>

<script>
    let moduleCount = document.querySelectorAll('.module-item').length;

    function attachRemoveEvent(btn, element) {
        btn?.addEventListener('click', function () {
            element.remove();
        });
    }

    // Add Feature
    document.getElementById('add-feature')?.addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'feature-item flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="features[]" class="form-input flex-1">
            <button type="button" class="remove-feature text-red-500">Remove</button>
        `;
        document.getElementById('features-container').appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-feature'), div);
    });

    // Add Objective
    document.getElementById('add-objective')?.addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'feature-item flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="learning_objectives[]" class="form-input flex-1">
            <button type="button" class="remove-objective text-red-500">Remove</button>
        `;
        document.getElementById('objectives-container').appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-objective'), div);
    });

    // Add Prerequisite
    document.getElementById('add-prerequisite')?.addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'feature-item flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="prerequisites[]" class="form-input flex-1">
            <button type="button" class="remove-prerequisite text-red-500">Remove</button>
        `;
        document.getElementById('prerequisites-container').appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-prerequisite'), div);
    });

    // Add Module
    document.getElementById('add-module')?.addEventListener('click', function () {

        const div = document.createElement('div');
        div.className = 'module-item';

        div.innerHTML = `
            <div class="mb-2">
                <label class="form-label text-sm">Module Title</label>
                <input type="text" name="modules[${moduleCount}][title]" class="form-input">
            </div>

            <div class="mb-2">
                <label class="form-label text-sm">Module Content</label>
                <textarea name="modules[${moduleCount}][content]" rows="3" class="form-textarea"></textarea>
            </div>

            <div>
                <label class="form-label text-sm">Video URL</label>
                <input type="url" name="modules[${moduleCount}][video_url]" class="form-input">
            </div>

            <button type="button" class="remove-module text-red-500 text-sm mt-2">
                Remove Module
            </button>
        `;

        document.getElementById('modules-container').appendChild(div);

        attachRemoveEvent(div.querySelector('.remove-module'), div);

        moduleCount++;
    });

    // Existing remove buttons
    document.querySelectorAll('.remove-feature').forEach(btn => {
        btn.addEventListener('click', function () {
            this.parentElement.remove();
        });
    });

    document.querySelectorAll('.remove-objective').forEach(btn => {
        btn.addEventListener('click', function () {
            this.parentElement.remove();
        });
    });

    document.querySelectorAll('.remove-prerequisite').forEach(btn => {
        btn.addEventListener('click', function () {
            this.parentElement.remove();
        });
    });

    document.querySelectorAll('.remove-module').forEach(btn => {
        btn.addEventListener('click', function () {
            this.parentElement.remove();
        });
    });
</script>
@endsection