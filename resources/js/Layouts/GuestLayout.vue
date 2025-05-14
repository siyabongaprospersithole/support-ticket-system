<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { onMounted } from 'vue';

// Force layout refresh on component mount to ensure proper rendering after navigation
onMounted(() => {
  // This timeout helps ensure layout is properly applied after reactivity changes
  setTimeout(() => {
    window.dispatchEvent(new Event('resize'));
  }, 50);
});
</script>

<template>
  <div class="min-h-screen flex flex-col md:flex-row bg-slate-50">
    <!-- Left: Illustration & Info -->
    <div class="hidden md:block md:w-1/2 h-screen bg-[#1455C0] relative overflow-hidden">
      <!-- Swiffy-style gradient overlay -->
      <div class="absolute inset-0 bg-gradient-to-br from-[#1455C0] via-[#0F46A0] to-[#083277] opacity-90"></div>
      
      <!-- Floating Cards/Icons -->
      <div class="absolute top-10 left-10 animate-float-slow z-10">
        <div class="stylish-card">
          <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full opacity-40 blur-md"></div>
          <div class="absolute -bottom-2 -left-2 w-6 h-6 bg-gradient-to-tr from-blue-300 to-cyan-400 rounded-full opacity-30 blur-sm"></div>
          <div class="card-shine"></div>
          <div class="flex items-center justify-center bg-blue-500/10 p-3 rounded-xl mb-3">
            <svg class="w-10 h-10 text-[#1455C0] drop-shadow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 5H7C5.895 5 5 5.895 5 7v12c0 1.105.895 2 2 2h10c1.105 0 2-.895 2-2V9l-4-4z"/><path d="M15 5v4h4"/><path d="M9 13h6M9 17h6"/></svg>
          </div>
          <div class="font-bold text-gray-800 text-base mb-2">Automated Ticketing</div>
          <ul class="text-xs text-gray-600 mt-1 space-y-1.5">
            <li class="flex items-center">
              <svg class="w-3 h-3 text-[#1455C0] mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Fast assignment
            </li>
            <li class="flex items-center">
              <svg class="w-3 h-3 text-[#1455C0] mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Smart routing
            </li>
            <li class="flex items-center">
              <svg class="w-3 h-3 text-[#1455C0] mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Instant updates
            </li>
          </ul>
        </div>
      </div>
      <div class="absolute bottom-40 left-1/3 animate-float-fast z-10">
        <div class="stylish-card stylish-card-row">
          <div class="absolute -bottom-3 -left-3 w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-40 blur-md"></div>
          <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-tr from-green-300 to-teal-400 rounded-full opacity-30 blur-sm"></div>
          <div class="card-shine"></div>
          <div class="flex items-center justify-center bg-green-500/10 p-3 rounded-xl mr-3">
            <svg class="w-8 h-8 text-green-600 drop-shadow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 11.5c.003 1.32-.305 2.622-.9 3.8-.706 1.412-1.79 2.6-3.133 3.43C15.625 19.56 14.078 20 12.5 20c-1.32.004-2.622-.305-3.8-.9L3 21l1.9-5.7C4.305 14.12 3.997 12.82 4 11.5c.001-1.578.441-3.125 1.271-4.468C6.1 5.69 7.288 4.606 8.7 3.9c1.178-.595 2.48-.903 3.8-.9h.5c2.084.115 4.053.995 5.53 2.471C20.005 6.947 20.885 8.916 21 11v.5z"/></svg>
          </div>
          <div>
            <span class="text-sm font-bold text-gray-800">WhatsApp Chat</span>
            <p class="text-xs text-gray-600 mt-0.5">Real-time support</p>
          </div>
        </div>
      </div>
      <div class="absolute top-1/3 right-10 animate-float-mid z-10">
        <div class="stylish-card stylish-card-row">
          <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-full opacity-40 blur-md"></div>
          <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-gradient-to-tr from-yellow-300 to-amber-400 rounded-full opacity-30 blur-sm"></div>
          <div class="card-shine"></div>
          <div class="flex items-center justify-center bg-yellow-500/10 p-3 rounded-xl mr-3">
            <svg class="w-8 h-8 text-yellow-600 drop-shadow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 18v-6c0-2.387 0-4.676 1.688-6.364C7.324 3.948 9.613 3 12 3c2.387 0 4.676.948 6.364 2.636C21.052 7.324 22 9.613 22 12v6"/><path d="M21 19c0 .53-.21 1.04-.586 1.414A2 2 0 0 1 19 21h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3v5zM3 19c0 .53.21 1.04.586 1.414A2 2 0 0 0 5 21h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3v5z"/></svg>
          </div>
        <div>
            <span class="text-sm font-bold text-gray-800">Priority Support</span>
            <p class="text-xs text-gray-600 mt-0.5">Dedicated assistance</p>
          </div>
        </div>
      </div>
      
      <!-- Decorative elements inspired by Swiffy -->
      <div class="absolute inset-0 z-0">
        <span v-for="n in 12" :key="n" :class="`absolute w-2 h-2 rounded-full opacity-60`" :style="{
          left: `${Math.random()*100}%`,
          top: `${Math.random()*100}%`,
          background: ['#fbbf24','#34d399','#60a5fa','#f87171','#a78bfa'][n%5]
        }"></span>
      </div>
      
      <!-- Swiffy tagline section -->
      <div class="absolute bottom-10 left-0 right-0 text-center z-20">
        <h2 class="text-white text-3xl font-bold mb-2 drop-shadow">Everything payments</h2>
        <p class="text-blue-100 text-sm max-w-xs mx-auto">Swiffy makes payments easy!</p>
        <div class="flex justify-center mt-3 space-x-3">
          <span class="text-white text-xs font-medium">Deposits</span>
          <span class="text-white/50">|</span>
          <span class="text-white text-xs font-medium">Payouts</span>
          <span class="text-white/50">|</span>
          <span class="text-white text-xs font-medium">Settlements</span>
        </div>
      </div>
    </div>

    <!-- Right: Auth Form -->
    <div class="flex flex-col justify-center items-center w-full md:w-1/2 h-screen bg-white shadow-xl z-20 px-4 md:px-0">
      <div class="w-full max-w-md mx-auto p-6 md:p-8 rounded-2xl shadow-lg bg-white">
        <div class="flex flex-col items-center mb-6 md:mb-8">
          <!-- Swiffy Logo -->
          <div class="w-28 md:w-32 h-14 md:h-16 mb-4 md:mb-6 relative">
            <svg viewBox="0 0 100 50" class="w-full h-full">
              <path d="M20.5,15 C15,15 10.5,19.5 10.5,25 C10.5,30.5 15,35 20.5,35 L25,35 C27.5,35 29.5,33 29.5,30.5 C29.5,28 27.5,26 25,26 L22.5,26 C21.5,26 20.5,25 20.5,24 C20.5,23 21.5,22 22.5,22 L35,22 C37.5,22 39.5,20 39.5,17.5 C39.5,16 38.5,15 37.5,15 L20.5,15 Z" fill="#1455C0"/>
              <path d="M42.5,15 C40,15 38,17 38,19.5 C38,22 40,24 42.5,24 L57.5,24 C58.5,24 59.5,25 59.5,26 C59.5,27 58.5,28 57.5,28 L45,28 C42.5,28 40.5,30 40.5,32.5 C40.5,34 41.5,35 42.5,35 L57.5,35 C63,35 67.5,30.5 67.5,25 C67.5,19.5 63,15 57.5,15 L42.5,15 Z" fill="#1455C0"/>
              <path d="M72.5,15 C70,15 68,17 68,19.5 C68,22 70,24 72.5,24 L77.5,24 C78.5,24 79.5,25 79.5,26 C79.5,27 78.5,28 77.5,28 L70,28 C67.5,28 65.5,30 65.5,32.5 C65.5,34 66.5,35 67.5,35 L77.5,35 C83,35 87.5,30.5 87.5,25 C87.5,19.5 83,15 77.5,15 L72.5,15 Z" fill="#1455C0"/>
            </svg>
          </div>
          <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">Hello Again!</h1>
          <p class="text-gray-400 text-sm text-center">Security & Safety Guaranteed with Swiffy's support system</p>
        </div>
        <slot />
      </div>
      <div class="mt-6 text-center text-gray-400 text-xs">
        &copy; {{ new Date().getFullYear() }} Swiffy. All rights reserved.
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes float-slow {
  0%,100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}
@keyframes float-mid {
  0%,100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
@keyframes float-fast {
  0%,100% { transform: translateY(0); }
  50% { transform: translateY(-30px); }
}
.animate-float-slow { animation: float-slow 6s ease-in-out infinite; }
.animate-float-mid { animation: float-mid 4s ease-in-out infinite; }
.animate-float-fast { animation: float-fast 3s ease-in-out infinite; }
.stylish-card {
  @apply relative bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-5 flex flex-col items-center transition-all duration-300 hover:scale-105 hover:shadow-2xl border border-white/40;
  box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1), 0 5px 15px -5px rgba(0, 0, 0, 0.05);
}

.stylish-card:hover {
  box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.2), 0 10px 20px -5px rgba(0, 0, 0, 0.1);
}

.stylish-card-row {
  @apply flex-row items-center justify-start p-4;
}

.card-shine {
  @apply absolute inset-0 rounded-2xl overflow-hidden pointer-events-none;
  background: linear-gradient(
    110deg,
    rgba(255, 255, 255, 0) 25%,
    rgba(255, 255, 255, 0.1) 30%,
    rgba(255, 255, 255, 0.2) 35%, 
    rgba(255, 255, 255, 0.5) 45%,
    rgba(255, 255, 255, 0.2) 50%, 
    rgba(255, 255, 255, 0.1) 55%,
    rgba(255, 255, 255, 0) 60%
  );
  background-position: -300px -300px;
  background-size: 250% 250%;
  transition: background-position 0.5s ease-out;
}

.stylish-card:hover .card-shine {
  background-position: 100% 100%;
}
</style>
