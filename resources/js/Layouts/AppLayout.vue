<template>
  <div class="min-h-screen bg-base-200 flex">
    <!-- Sidebar -->
    <aside class="w-20 md:w-64 bg-white border-r border-base-300 flex flex-col items-center md:items-stretch py-4 px-2 md:px-0 shadow-lg z-20 animate-fade-in">
      <div class="flex items-center justify-center mb-8">
        <img src="/logo.svg" alt="Logo" class="h-10 w-10 md:h-12 md:w-12 rounded-full shadow-md" />
      </div>
      <nav class="flex-1 flex flex-col gap-2">
        <router-link to="/dashboard" class="btn btn-ghost btn-md flex items-center gap-2 w-full justify-start">
          <span class="hidden md:inline">Dashboard</span>
          <i class="mdi mdi-view-dashboard-outline text-xl md:mr-2"></i>
        </router-link>
        <router-link to="/tickets" class="btn btn-ghost btn-md flex items-center gap-2 w-full justify-start">
          <span class="hidden md:inline">Tickets</span>
          <i class="mdi mdi-ticket-outline text-xl md:mr-2"></i>
        </router-link>
        <!-- Add more nav links as needed -->
      </nav>
      <div class="mt-auto flex flex-col items-center md:items-start gap-2">
        <div class="avatar online">
          <div class="w-10 rounded-full">
            <img :src="$page.props.auth.user.avatar || '/default-avatar.png'" alt="User avatar" />
          </div>
        </div>
        <div class="hidden md:block mt-2">
          <div class="font-semibold">{{$page.props.auth.user.name}}</div>
          <div class="text-xs text-gray-400">{{$page.props.auth.user.email}}</div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
      <!-- Topbar -->
      <header class="w-full bg-white border-b border-base-300 px-4 py-2 flex items-center justify-between shadow-sm animate-slide-down z-10">
        <div class="flex items-center gap-2">
          <button class="btn btn-ghost btn-circle md:hidden">
            <i class="mdi mdi-menu text-2xl"></i>
          </button>
          <slot name="header"></slot>
        </div>
        <div class="flex items-center gap-2">
          <button class="btn btn-ghost btn-circle">
            <i class="mdi mdi-bell-outline text-xl"></i>
          </button>
          <button class="btn btn-ghost btn-circle">
            <i class="mdi mdi-magnify text-xl"></i>
          </button>
          <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle avatar">
              <div class="w-8 rounded-full">
                <img :src="$page.props.auth.user.avatar || '/default-avatar.png'" />
              </div>
            </label>
            <ul tabindex="0" class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-base-100 rounded-box w-52">
              <li><a href="/profile">Profile</a></li>
              <li><a href="/logout">Logout</a></li>
            </ul>
          </div>
        </div>
      </header>
      <!-- Main Page Content -->
      <main class="flex-1 p-4 md:p-8 animate-fade-in">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
// No script needed for layout
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fade-in 0.5s;
}
@keyframes slide-down {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-down {
  animation: slide-down 0.5s;
}
</style> 