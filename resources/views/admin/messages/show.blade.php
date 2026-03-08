@extends('layouts.admin')

@section('title', 'Chat with ' . $user->name)

@section('content')
<div class="flex flex-col h-[calc(100vh-200px)] bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden" 
     x-data="adminChat({{ $user->id }}, {{ Auth::id() }})">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.messages.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>
            <span class="text-sm text-gray-500">({{ $user->email }})</span>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-4" x-ref="messagesContainer">
        <template x-for="msg in messages" :key="msg.id">
            <div class="flex" :class="msg.is_admin_message ? 'justify-end' : 'justify-start'">
                <div class="max-w-[70%] rounded-lg px-4 py-2" 
                     :class="msg.is_admin_message ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-200 text-gray-800'">
                    <p class="text-sm" x-text="msg.message"></p>
                    <p class="text-xs mt-1 font-mono" :class="msg.is_admin_message ? 'text-emerald-100' : 'text-gray-400'" 
                       x-text="formatDate(msg.created_at)"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Input Area -->
    <div class="p-4 bg-white border-t border-gray-100">
        <form @submit.prevent="sendMessage" class="flex space-x-4">
            <input type="text" x-model="newMessage" 
                   class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" 
                   placeholder="Type your reply..." :disabled="sending" required autofocus>
            <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition disabled:opacity-50" 
                    :disabled="sending || !newMessage.trim()">
                <span x-show="!sending">Send</span>
                <span x-show="sending">...</span>
            </button>
        </form>
    </div>
</div>

<script>
    function adminChat(userId, currentAdminId) {
        return {
            messages: @json($messages),
            newMessage: '',
            sending: false,
            userId: userId,

            init() {
                this.$nextTick(() => this.scrollToBottom());

                if (window.Echo) {
                    window.Echo.private(`chat.${this.userId}`)
                        .listen('.message.sent', (e) => {
                            // Only add if it's not from us (though toOthers() usually handles this, 
                            // broadcasting on the USER'S channel means the admin needs to listen there too)
                            if (e.message.sender_id !== currentAdminId) {
                                this.messages.push(e.message);
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        });
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim()) return;

                const messageContent = this.newMessage;
                this.sending = true;
                this.newMessage = '';

                try {
                    const response = await fetch(`{{ route('admin.messages.store', $user) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: messageContent })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.messages.push(data);
                        this.$nextTick(() => this.scrollToBottom());
                    } else {
                        this.newMessage = messageContent;
                        alert('Failed to send message.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.newMessage = messageContent;
                } finally {
                    this.sending = false;
                }
            },

            scrollToBottom() {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        }
    }
</script>
@endsection
