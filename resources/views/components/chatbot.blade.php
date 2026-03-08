<div
    x-data="chatBot()"
    x-init="initChat()"
    class="fixed bottom-6 right-6 z-50 flex flex-col items-end"
    x-cloak
>
    <!-- Chat Window -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="bg-white rounded-2xl shadow-2xl w-80 sm:w-96 mb-4 overflow-hidden border border-gray-100 flex flex-col h-[500px]"
    >
        <!-- Header -->
        <div class="bg-emerald-600 p-4 flex justify-between items-center text-white">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></div>
                <h3 class="font-semibold">Safari Assistant</h3>
            </div>
            <button @click="isOpen = false" class="hover:text-emerald-100 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50"
            x-ref="scrollContainer"
        >
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        :class="msg.role === 'user'
                            ? 'bg-emerald-600 text-white rounded-br-none'
                            : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none'"
                        class="max-w-[80%] rounded-2xl px-4 py-2 shadow-sm text-sm"
                    >
                        <!-- Use x-html for assistant to render markdown links if you parse them, or just text -->
                        <div x-text="msg.content" class="whitespace-pre-wrap"></div>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white border border-gray-200 px-4 py-3 rounded-2xl rounded-bl-none shadow-sm flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100">
            <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                <input
                    type="text"
                    x-model="newMessage"
                    :disabled="isLoading"
                    placeholder="Ask about safaris..."
                    class="flex-1 border-gray-200 rounded-full text-sm focus:border-emerald-500 focus:ring-emerald-500 disabled:opacity-50"
                >
                <button
                    type="submit"
                    :disabled="isLoading || !newMessage.trim()"
                    class="bg-emerald-600 text-white p-2 w-9 h-9 rounded-full hover:bg-emerald-700 transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button -->
    <button
        @click="toggleChat"
        class="bg-emerald-600 hover:bg-emerald-700 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105"
    >
        <i class="fas" :class="isOpen ? 'fa-times text-xl' : 'fa-comment-alt text-2xl'"></i>
    </button>
</div>

<script>
    function chatBot() {
        return {
            isOpen: false,
            isLoading: false,
            newMessage: '',
            messages: [
                { role: 'assistant', content: 'Jambo! 🌍 I\'m here to help you plan your dream safari. Ask me about our tours, pricing, or travel tips!' }
            ],

            initChat() {
                // Load history if needed, or start fresh
                const saved = localStorage.getItem('safari_chat_history');
                if (saved) {
                    this.messages = JSON.parse(saved);
                }
            },

            toggleChat() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            },

            async sendMessage() {
                const text = this.newMessage.trim();
                if (!text) return;

                this.messages.push({ role: 'user', content: text });
                this.newMessage = '';
                this.isLoading = true;
                this.scrollToBottom();

                try {
                    const response = await fetch('/api/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            // Add CSRF token just in case, though API mainly uses tokens if stateless
                            // For internal API usage in Blade, standard session auth or CSRF might be needed if web middleware used
                            // But here we set up api routes.
                        },
                        body: JSON.stringify({
                            message: text,
                            history: this.messages.slice(-6) // Send last 6 messages context
                        })
                    });

                    if (!response.ok) throw new Error('Network error');

                    const data = await response.json();

                    if (data.success) {
                        this.messages.push({ role: 'assistant', content: data.response });
                    } else {
                        // Fallback carried by backend usually, but if error flag:
                        this.messages.push({ role: 'assistant', content: data.response || "Sorry, I'm having trouble connecting right now." });
                    }

                    // Save to local storage
                    localStorage.setItem('safari_chat_history', JSON.stringify(this.messages));

                } catch (error) {
                    console.error('Chat error:', error);
                    this.messages.push({ role: 'assistant', content: "Sorry, I'm having trouble connecting to the server. Please try again." });
                } finally {
                    this.isLoading = false;
                    this.scrollToBottom();
                }
            },

            scrollToBottom() {
                const container = this.$refs.scrollContainer;
                container.scrollTop = container.scrollHeight;
            }
        }
    }
</script>
