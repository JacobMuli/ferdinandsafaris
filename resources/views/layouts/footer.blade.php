<footer class="bg-gray-900  text-white  py-12 mt-20 border-t border-gray-100 ">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold mb-4">Ferdinand Safaris</h3>
                <p class="text-gray-400 ">Creating unforgettable African adventures since 2008</p>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-emerald-400">Quick Links</h4>
                <ul class="space-y-2 text-gray-400 ">
                    <li><a href="{{ route('tours.index') }}" class="hover:text-white  transition">Tours</a></li>
                    <li><a href="#" class="hover:text-white  transition">About Us</a></li>
                    <li><a href="#" class="hover:text-white  transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-emerald-400">Contact</h4>
                <ul class="space-y-2 text-gray-400 ">
                    <li><i class="fas fa-envelope mr-2"></i>info@ferdinandsafaris.com</li>
                    <li><i class="fas fa-phone mr-2"></i>+254-720-968563</li>
                    <li><i class="fas fa-map-marker-alt mr-2"></i>Nairobi, Kenya</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-emerald-400">Follow Us</h4>
                <div class="flex space-x-6">
                    <a href="#" class="text-3xl hover:text-emerald-400 transition"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-3xl hover:text-emerald-400 transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-3xl hover:text-emerald-400 transition"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" class="text-3xl hover:text-emerald-400 transition"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800  mt-8 pt-8 text-center text-gray-400 ">
            <p>&copy; {{ date('Y') }} Ferdinand Safaris. All rights reserved.</p>
        </div>
    </div>
</footer>