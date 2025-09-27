<div class="space-y-4">
    <div class="text-sm text-gray-600">
        <strong>User:</strong> {{ $user->name ?? 'Unknown' }}<br>
        <strong>Email:</strong> {{ $user->email ?? 'N/A' }}<br>
        <strong>Last Login:</strong> {{ $user->last_login ?? 'Never' }}
    </div>

    <div class="border-t pt-4">
        <h4 class="font-medium text-gray-900 mb-3">Recent Activity</h4>

        <div class="space-y-3">
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="text-sm">
                    <div class="text-gray-900">Logged in successfully</div>
                    <div class="text-gray-500">2 hours ago</div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="text-sm">
                    <div class="text-gray-900">Updated profile information</div>
                    <div class="text-gray-500">1 day ago</div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="text-sm">
                    <div class="text-gray-900">Password changed</div>
                    <div class="text-gray-500">3 days ago</div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                <div class="text-sm">
                    <div class="text-gray-900">Role changed to Editor</div>
                    <div class="text-gray-500">1 week ago</div>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-gray-400 rounded-full mt-2 flex-shrink-0"></div>
                <div class="text-sm">
                    <div class="text-gray-900">Account created</div>
                    <div class="text-gray-500">{{ $user->created_at ?? '2 weeks ago' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t pt-4">
        <div class="text-xs text-gray-500">
            This is a demo activity log. In a real application, this would show actual user activity data.
        </div>
    </div>
</div>