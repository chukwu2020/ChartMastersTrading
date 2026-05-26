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

    .feature-item, .module-item {
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
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Create New Trading Course</h5>
            <p class="text-sm text-gray-500 mt-1">Add a new educational course or trading strategy</p>
        </div>
        <ul class="flex items-center gap-[6px]">
            <li><a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;">
                <iconify-icon icon="solar:home-smile-angle-outline"></iconify-icon> Dashboard</a>
            </li>
            <li>-</li>
            <li><a href="{{ route('admin.strategies.strategyindex') }}" class="hover:text-[#9EDD05]" style="color:#0C3A30;">Courses</a></li>
            <li>-</li>
            <li class="font-medium" style="color:#9EDD05;">Create</li>
        </ul>
    </div>

    <form action="{{ route('admin.strategies.strategystore') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Course Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input" required value="{{ old('name') }}">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-input" placeholder="auto-generated" value="{{ old('slug') }}">
                    <p class="text-xs text-gray-400 mt-1">Leave empty for auto-generation</p>
                </div>
                <div>
                    <label class="form-label">Price (USD) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" step="0.01" class="form-input" required value="{{ old('price') }}">
                </div>
                <div>
                    <label class="form-label">Duration (Days)</label>
                    <input type="number" name="duration_days" class="form-input" placeholder="Leave empty for lifetime access" value="{{ old('duration_days') }}">
                </div>
                <div>
                    <label class="form-label">Difficulty Level</label>
                    <select name="difficulty_level" class="form-select">
                        <option value="">Select Difficulty</option>
                        <option value="beginner">Beginner</option>
                         <option value="beginner">Beginner To Intermediate</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
               
                <div>
    <label class="form-label">Estimated Hours</label>
    <input type="text" name="estimated_hours" class="form-input" placeholder="e.g., 10 Hours, Self Paced, Unlimited Access" value="{{ old('estimated_hours') }}">
    <p class="text-xs text-gray-400 mt-1">Examples: "10 Hours", "Self Paced", "Unlimited Access", "8 Weeks"</p>
</div>
                <div>
                    <label class="form-label">Instructor Name</label>
                    <input type="text" name="instructor_name" class="form-input" value="{{ old('instructor_name') }}">
                </div>
                <div>
                    <label class="form-label">Badge Text</label>
                    <input type="text" name="badge_text" class="form-input" placeholder="e.g., MOST POPULAR, NEW, HOT" value="{{ old('badge_text') }}">
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked> 
                        <span class="text-sm">Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_popular" value="1"> 
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
                <textarea name="description" rows="3" class="form-textarea" required>{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="form-label">Long Description</label>
                <textarea name="long_description" rows="6" class="form-textarea">{{ old('long_description') }}</textarea>
            </div>
        </div>

        <!-- Images -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Images</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Cover Image</label>
                    <input type="file" name="cover_image" class="form-input" accept="image/*">
                    <p class="text-xs text-gray-400 mt-1">Recommended: 800x400px</p>
                </div>
                <div>
                    <label class="form-label">Instructor Image</label>
                    <input type="file" name="instructor_image" class="form-input" accept="image/*">
                    <p class="text-xs text-gray-400 mt-1">Recommended: 200x200px</p>
                </div>
            </div>
        </div>

        <!-- Instructor Bio -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Instructor Information</h3>
            <div>
                <label class="form-label">Instructor Bio</label>
                <textarea name="instructor_bio" rows="4" class="form-textarea">{{ old('instructor_bio') }}</textarea>
            </div>
        </div>

        <!-- Features -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Course Features</h3>
            <div id="features-container">
                <div class="feature-item flex items-center gap-2">
                    <input type="text" name="features[]" class="form-input flex-1" placeholder="e.g., Full course access">
                    <button type="button" class="remove-feature text-red-500 hover:text-red-700">Remove</button>
                </div>
            </div>
            <button type="button" id="add-feature" class="add-btn">+ Add Feature</button>
        </div>

        <!-- Learning Objectives -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Learning Objectives</h3>
            <div id="objectives-container">
                <div class="feature-item flex items-center gap-2">
                    <input type="text" name="learning_objectives[]" class="form-input flex-1" placeholder="e.g., Understand market structure">
                    <button type="button" class="remove-objective text-red-500 hover:text-red-700">Remove</button>
                </div>
            </div>
            <button type="button" id="add-objective" class="add-btn">+ Add Learning Objective</button>
        </div>

        <!-- Prerequisites -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Prerequisites</h3>
            <div id="prerequisites-container">
                <div class="feature-item flex items-center gap-2">
                    <input type="text" name="prerequisites[]" class="form-input flex-1" placeholder="e.g., Basic trading knowledge">
                    <button type="button" class="remove-prerequisite text-red-500 hover:text-red-700">Remove</button>
                </div>
            </div>
            <button type="button" id="add-prerequisite" class="add-btn">+ Add Prerequisite</button>
        </div>

        <!-- Modules -->
        <div class="form-card">
            <h3 class="text-lg font-bold mb-4" style="color: var(--dark-green);">Course Modules</h3>
            <div id="modules-container">
                <div class="module-item">
                    <div class="mb-2">
                        <label class="form-label text-sm">Module Title</label>
                        <input type="text" name="modules[0][title]" class="form-input" placeholder="Module 1: Introduction">
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-sm">Module Content</label>
                        <textarea name="modules[0][content]" rows="3" class="form-textarea" placeholder="Module content description..."></textarea>
                    </div>
                    <div>
                        <label class="form-label text-sm">Video URL (Optional)</label>
                        <input type="url" name="modules[0][video_url]" class="form-input" placeholder="https://youtube.com/...">
                    </div>
                    <button type="button" class="remove-module text-red-500 text-sm mt-2">Remove Module</button>
                </div>
            </div>
            <button type="button" id="add-module" class="add-btn">+ Add Module</button>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.strategies.strategyindex') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="btn-submit">Create Course</button>
        </div>
    </form>
</div>

<script>
    let featureCount = 1;
    let objectiveCount = 1;
    let prerequisiteCount = 1;
    let moduleCount = 1;

    // Add Feature
    document.getElementById('add-feature')?.addEventListener('click', function() {
        const container = document.getElementById('features-container');
        const div = document.createElement('div');
        div.className = 'feature-item flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="features[]" class="form-input flex-1" placeholder="e.g., Full course access">
            <button type="button" class="remove-feature text-red-500 hover:text-red-700">Remove</button>
        `;
        container.appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-feature'), div);
    });

    // Add Learning Objective
    document.getElementById('add-objective')?.addEventListener('click', function() {
        const container = document.getElementById('objectives-container');
        const div = document.createElement('div');
        div.className = 'feature-item flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="learning_objectives[]" class="form-input flex-1" placeholder="e.g., Understand market structure">
            <button type="button" class="remove-objective text-red-500 hover:text-red-700">Remove</button>
        `;
        container.appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-objective'), div);
    });

    // Add Prerequisite
    document.getElementById('add-prerequisite')?.addEventListener('click', function() {
        const container = document.getElementById('prerequisites-container');
        const div = document.createElement('div');
        div.className = 'feature-item flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="prerequisites[]" class="form-input flex-1" placeholder="e.g., Basic trading knowledge">
            <button type="button" class="remove-prerequisite text-red-500 hover:text-red-700">Remove</button>
        `;
        container.appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-prerequisite'), div);
    });

    // Add Module
    document.getElementById('add-module')?.addEventListener('click', function() {
        moduleCount++;
        const container = document.getElementById('modules-container');
        const div = document.createElement('div');
        div.className = 'module-item';
        div.innerHTML = `
            <div class="mb-2">
                <label class="form-label text-sm">Module Title</label>
                <input type="text" name="modules[${moduleCount}][title]" class="form-input" placeholder="Module ${moduleCount + 1}: ...">
            </div>
            <div class="mb-2">
                <label class="form-label text-sm">Module Content</label>
                <textarea name="modules[${moduleCount}][content]" rows="3" class="form-textarea" placeholder="Module content description..."></textarea>
            </div>
            <div>
                <label class="form-label text-sm">Video URL (Optional)</label>
                <input type="url" name="modules[${moduleCount}][video_url]" class="form-input" placeholder="https://youtube.com/...">
            </div>
            <button type="button" class="remove-module text-red-500 text-sm mt-2">Remove Module</button>
        `;
        container.appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-module'), div);
    });

    function attachRemoveEvent(btn, element) {
        btn?.addEventListener('click', function() {
            element.remove();
        });
    }

    // Attach remove events to existing elements
    document.querySelectorAll('.remove-feature').forEach(btn => {
        btn.addEventListener('click', function() { this.parentElement.remove(); });
    });
    document.querySelectorAll('.remove-objective').forEach(btn => {
        btn.addEventListener('click', function() { this.parentElement.remove(); });
    });
    document.querySelectorAll('.remove-prerequisite').forEach(btn => {
        btn.addEventListener('click', function() { this.parentElement.remove(); });
    });
    document.querySelectorAll('.remove-module').forEach(btn => {
        btn.addEventListener('click', function() { this.parentElement.remove(); });
    });
</script>
@endsection