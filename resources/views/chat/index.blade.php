<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Support Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[600px] flex flex-col" x-data="chatSystem()">
                <!-- Chat Messages -->
                <div class="flex-1 p-6 overflow-y-auto bg-gray-50" x-ref="chatContainer">
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="mb-4 flex" :class="msg.is_admin_message ? 'justify-start' : 'justify-end'">
                            <div class="max-w-[70%] rounded-lg px-4 py-2"
                                 :class="msg.is_admin_message ? 'bg-white border border-gray-200 text-gray-800' : 'bg-emerald-600 text-white'">
                                <p class="text-sm" x-text="msg.message"></p>
                                <p class="text-xs mt-1" :class="msg.is_admin_message ? 'text-gray-500' : 'text-emerald-100'" x-text="formatDate(msg.created_at)"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="messages.length === 0 && !loading" class="text-center text-gray-500 mt-10">
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t border-gray-200 bg-white">
                    <form @submit.prevent="sendMessage" class="flex space-x-4">
                        <input type="text"
                               x-model="newMessage"
                               class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="Type your message here..."
                               :disabled="sending">
                        <button type="submit"
                                class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
                                :disabled="sending || !newMessage.trim()">
                            <span x-show="!sending">Send</span>
                            <span x-show="sending">...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function chatSystem() {
            return {
                messages: [],
                newMessage: '',
                loading: true,
                sending: false,
                pollInterval: null,

                init() {
                    this.fetchMessages();
                    
                    // Listen for real-time messages
                    if (window.Echo) {
                        window.Echo.private(`chat.{{ auth()->id() }}`)
                            .listen('.message.sent', (e) => {
                                console.log('New message received:', e);
                                this.messages.push(e.message);
                                this.$nextTick(() => {
                                    this.scrollToBottom();
                                });
                            });
                    }
                },

                async fetchMessages() {
                    try {
                        const response = await fetch('{{ route("chat.fetch") }}');
                        const data = await response.json();
                        this.messages = data;
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                        this.loading = false;
                    } catch (error) {
                        console.error('Error fetching messages:', error);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim()) return;

                    const messageContent = this.newMessage;
                    this.sending = true;
                    this.newMessage = '';

                    try {
                        const response = await fetch('{{ route("chat.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message: messageContent
                            })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.messages.push(data);
                            this.$nextTick(() => {
                                this.scrollToBottom();
                            });
                        } else {
                            this.newMessage = messageContent; // Restore message on error
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        this.newMessage = messageContent;
                    } finally {
                        this.sending = false;
                    }
                },

                scrollToBottom() {
                    const container = this.$refs.chatContainer;
                    container.scrollTop = container.scrollHeight;
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
</x-app-layout>
