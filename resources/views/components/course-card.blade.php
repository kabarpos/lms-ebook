<a href="{{ route('front.course.details', $course->slug) }}" class="group block transition-all duration-200 hover:-translate-y-1">
    <div class="course-card flex flex-col rounded-xl border border-gray-200 hover:border-lochmara-300 hover:shadow-lg transition-all duration-200 bg-white overflow-hidden">
        <div class="thumbnail-container p-4">
            <div class="relative w-full h-40 rounded-lg overflow-hidden bg-gray-100">
                @if($course->thumbnail)
                    @if(str_starts_with($course->thumbnail, 'http'))
                        <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="thumbnail">
                    @else
                        <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="thumbnail">
                    @endif
                @else
                    <div class="w-full h-full flex items-center justify-center bg-lochmara-100">
                        <span class="text-lochmara-600 font-bold text-xl">{{ substr($course->name, 0, 2) }}</span>
                    </div>
                @endif
                <div class="absolute top-3 right-3 z-10 flex flex-col items-center rounded-lg py-1 px-2 bg-white bg-opacity-95 shadow-sm">
                    <svg class="w-4 h-4 text-yellow-500 mb-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-semibold text-xs text-gray-800">4.8</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col p-6 pt-2 space-y-4">
            <h3 class="font-semibold text-lg line-clamp-2 text-gray-900 group-hover:text-lochmara-700 transition-colors duration-200">{{ $course->name }}</h3>
            
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <div class="p-1.5 bg-lochmara-100 rounded-md">
                        <svg class="w-3 h-3 text-lochmara-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">{{ $course->category->name }}</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <div class="p-1.5 bg-gray-100 rounded-md">
                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">{{ $course->content_count }} Lessons</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <div class="p-1.5 bg-gray-100 rounded-md">
                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-600">Certificate</span>
                </div>
            </div>
            
            <!-- Call to Action -->
            <div class="pt-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ $course->courseStudents->count() }} students</span>
                    <span class="text-lochmara-600 font-semibold text-sm group-hover:text-lochmara-700 transition-colors duration-200">View Details →</span>
                </div>
            </div>
        </div>
    </div>
</a>
