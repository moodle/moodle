<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <title>e-RTU</title>
</head>

<body class="bg-white">
    <!-- NAVAGATION BAR -->
    <nav class="fixed z-30 w-full bg-gray-800 border-b border-gray-200">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
                        class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100  focus:ring-2 focus:ring-gray-100  ">
                        <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <a href="#" class="flex ml-2 md:mr-24">
                        <span
                            class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-[#FFD66E]">e-RTU</span>
                    </a>
                </div>
                <div class="flex items-center">

                    <button id="toggleSidebarMobileSearch" type="button"
                        class="p-2 text-white rounded-lg lg:hidden hover:text-gray-900 hover:bg-gray-100 ">
                    </button>

                    <button type="button" data-dropdown-toggle="notification-dropdown"
                        class="p-2 text-white rounded-lg hover:text-gray-900 hover:bg-gray-100 ">
                        <span class="sr-only">View notifications</span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
                            </path>
                        </svg>
                    </button>

                    <div class="z-20 z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-white divide-y divide-gray-100 rounded shadow-lg"
                        id="notification-dropdown">
                        <div class="block px-4 py-2 text-base font-medium text-center text-gray-700 bg-gray-50 ">
                            Notifications
                        </div>
                        <div>
                            <a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11"
                                        src="https://flowbite-admin-dashboard.vercel.app/images/users/bonnie-green.png"
                                        alt="Jese image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 border border-white rounded-full bg-primary-700 ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z">
                                            </path>
                                            <path
                                                d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 ">New message from <span
                                            class="font-semibold text-gray-900 text-white">Bonnie Green</span>: "Hey,
                                        what's up? All set for the presentation?"</div>
                                    <div class="text-xs font-medium text-primary-700 ">a few moments ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3  border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11"
                                        src="https://flowbite-admin-dashboard.vercel.app/images/users/jese-leos.png"
                                        alt="Jese image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-gray-900 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span
                                            class="font-semibold text-gray-900 text-white">Jese leos</span> and <span
                                            class="font-medium text-gray-900 text-white">5 others</span> started
                                        following you.</div>
                                    <div class="text-xs font-medium text-primary-700 ">10 minutes ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11"
                                        src="https://flowbite-admin-dashboard.vercel.app/images/users/joseph-mcfall.png"
                                        alt="Joseph image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-red-600 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span
                                            class="font-semibold text-gray-900 text-white">Joseph Mcfall</span> and
                                        <span class="font-medium text-gray-900 text-white">141 others</span> love your
                                        story. See it and view more stories.</div>
                                    <div class="text-xs font-medium text-primary-700 ">44 minutes ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3 border-b hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11"
                                        src="https://flowbite-admin-dashboard.vercel.app/images/users/leslie-livingston.png"
                                        alt="Leslie image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-green-400 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span
                                            class="font-semibold text-gray-900 text-white">Leslie Livingston</span>
                                        mentioned you in a comment: <span
                                            class="font-medium text-primary-700 ">@bonnie.green</span> what do you say?
                                    </div>
                                    <div class="text-xs font-medium text-primary-700 ">1 hour ago</div>
                                </div>
                            </a>
                            <a href="#" class="flex px-4 py-3 hover:bg-gray-100 ">
                                <div class="flex-shrink-0">
                                    <img class="rounded-full w-11 h-11"
                                        src="https://flowbite-admin-dashboard.vercel.app/images/users/robert-brown.png"
                                        alt="Robert image">
                                    <div
                                        class="absolute flex items-center justify-center w-5 h-5 ml-6 -mt-5 bg-purple-500 border border-white rounded-full ">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="w-full pl-3">
                                    <div class="text-gray-500 font-normal text-sm mb-1.5 "><span
                                            class="font-semibold text-gray-900 text-white">Robert Brown</span> posted a
                                        new video: Glassmorphism - learn how to implement the new design trend.</div>
                                    <div class="text-xs font-medium text-primary-700 ">3 hours ago</div>
                                </div>
                            </a>
                        </div>
                        <a href="#"
                            class="block py-2 text-base font-normal text-center text-gray-900 bg-gray-50 hover:bg-gray-100 text-white ">
                            <div class="inline-flex items-center ">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                View all
                            </div>
                        </a>
                    </div>
                    <button type="button" data-dropdown-toggle="apps-dropdown"
                        class="hidden p-2 text-white rounded-lg sm:flex hover:text-gray-900 hover:bg-gray-100 ">
                        <span class="sr-only">View notifications</span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                    </button>

                    <div class="z-20 z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-white divide-y divide-gray-100 rounded shadow-lg"
                        id="apps-dropdown">
                        <div class="block px-4 py-2 text-base font-medium text-center text-gray-700 bg-gray-50 ">
                            Apps
                        </div>
                        <div class="grid grid-cols-3 gap-4 p-4">
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Sales</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                                    </path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Users</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Inbox</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Profile</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Settings</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Products</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                                    </path>
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Pricing</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Billing</div>
                            </a>
                            <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 ">
                                <svg class="mx-auto mb-1 text-gray-500 w-7 h-7 " fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900 text-white">Logout</div>
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center ml-3">
                        <div>
                            <button type="button"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 "
                                id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full"
                                    src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                    alt="user photo">
                            </button>
                        </div>

                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow"
                            id="dropdown-2">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 text-white" role="none">
                                    Neil Sims
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate " role="none">
                                    neil.sims@flowbite.com
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  "
                                        role="menuitem">Dashboard</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  "
                                        role="menuitem">Settings</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  "
                                        role="menuitem">Earnings</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  "
                                        role="menuitem">Sign out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-t-8 border-[#FFD66E]">
    </nav>
    <!-- MAIN -->

    <div class="flex pt-16 overflow-hidden bg-gray-50 ">

        <aside id="sidebar"
            class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 hidden  w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width"
            aria-label="Sidebar">
            <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-gray-800 border-r border-gray-200">
                <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-1 px-3 space-y-1 bg-gray-800 divide-y divide-gray-200 ">
                        <ul class="pb-2 space-y-2">
                            <li>
                                <a href="<?php echo $CFG->wwwroot . '/local/auto_proctor/auto_proctor_dashboard.php'?>">
                                    <button type="button"
                                        class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700"
                                        aria-controls="dropdown-layouts" data-collapse-toggle="dropdown-layouts">
                                        <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 "
                                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                                            aria-hidden="true">
                                            <path
                                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                                            </path>
                                        </svg>
                                        <span class="flex-1 ml-3 text-left whitespace-nowrap"
                                            sidebar-toggle-item>Home</span>
                                    </button>
                                </a>
                            </li>
                            <li>
                                <button type="button"
                                    class="flex items-center w-full p-2 text-base text-gray-50 transition duration-75 rounded-lg group hover:bg-gray-100 hover:text-gray-700"
                                    aria-controls="dropdown-crud" data-collapse-toggle="dropdown-crud">
                                    <svg class="flex-shrink-0 w-6 h-6 text-gray-100 transition duration-75 group-hover:text-gray-900 "
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M.99 5.24A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25l.01 9.5A2.25 2.25 0 0116.76 17H3.26A2.267 2.267 0 011 14.74l-.01-9.5zm8.26 9.52v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75v.615c0 .414.336.75.75.75h5.373a.75.75 0 00.627-.74zm1.5 0a.75.75 0 00.627.74h5.373a.75.75 0 00.75-.75v-.615a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75v.625zm6.75-3.63v-.625a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75v.625c0 .414.336.75.75.75h5.25a.75.75 0 00.75-.75zm-8.25 0v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75v.625c0 .414.336.75.75.75H8.5a.75.75 0 00.75-.75zM17.5 7.5v-.625a.75.75 0 00-.75-.75H11.5a.75.75 0 00-.75.75V7.5c0 .414.336.75.75.75h5.25a.75.75 0 00.75-.75zm-8.25 0v-.625a.75.75 0 00-.75-.75H3.25a.75.75 0 00-.75.75V7.5c0 .414.336.75.75.75H8.5a.75.75 0 00.75-.75z">
                                        </path>
                                    </svg>
                                    <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Test
                                        Result</span>
                                    <svg sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <ul id="dropdown-crud" class="space-y-2 py-2 hidden ">
                                    <li>
                                        <a href="https://flowbite-admin-dashboard.vercel.app/crud/products/"
                                            class="text-base text-gray-900 rounded-lg flex items-center p-2 group hover:bg-gray-100 transition duration-75 pl-11 ">Products</a>
                                    </li>
                                    <li>
                                        <a href="https://flowbite-admin-dashboard.vercel.app/crud/users/"
                                            class="text-base text-gray-900 rounded-lg flex items-center p-2 group hover:bg-gray-100 transition duration-75 pl-11 ">Users</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>

        <div class="fixed inset-0 z-10 hidden bg-gray-900/50 /90" id="sidebarBackdrop"></div>
        <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 ">
            <main>
                <div class="p-4 bg-white block sm:flex items-center justify-between  lg:mt-1.5 ">
                    <div class="w-full mb-1">
                        <div class="pt-10">
                            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl ">Quiz Name #1</h1>
                            <span class="text-base font-normal text-gray-500 ">Report of all test takers and their
                                attempts</span>
                        </div>
                    </div>
                </div>
                <!-- for proctoreing setting -->
                <div class="p-4 bg-white">
                    <div
                        class=" border-t border-gray-400 border-b grid w-full grid-cols-1  mt-4 xl:grid-cols-3 2xl:grid-cols-3">
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Proctoring Settings : 
                                    <span><a href="" class="text-blue-700 text-base">Edit Settings</a></span></h3>
                                <span
                                    class="text-base font-md font-bold text-gray-700 ">
                                    <div class="flex space-x-6 sm:justify-center mt-2">
                                        <a href="#" class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[26px] h-[26px] text-gray-800 " viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="24" height="24" fill="white"/>
                                                <circle cx="12" cy="12" r="9" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 5.5V12H18" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[28px] h-[28px] text-gray-800 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="gray-800" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h10c.6 0 1-.4 1-1V7c0-.6-.4-1-1-1Zm7 11-6-2V9l6-2v10Z"/>
                                              </svg>
                                              
                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-gray-900  ">
                                            <svg fill="#000000" class="w-[26px] h-[26px] text-gray-800 " viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M425.818 709.983V943.41c0 293.551 238.946 532.497 532.497 532.497 293.55 0 532.496-238.946 532.496-532.497V709.983h96.818V943.41c0 330.707-256.438 602.668-580.9 627.471l-.006 252.301h242.044V1920H667.862v-96.818h242.043l-.004-252.3C585.438 1546.077 329 1274.116 329 943.41V709.983h96.818ZM958.315 0c240.204 0 435.679 195.475 435.679 435.68v484.087c0 240.205-195.475 435.68-435.68 435.68-240.204 0-435.679-195.475-435.679-435.68V435.68C522.635 195.475 718.11 0 958.315 0Z" fill-rule="evenodd"/>
                                            </svg>

                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[28px] h-[28px] text-gray-800 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M13 10c0-.6.4-1 1-1a1 1 0 1 1 0 2 1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                <path fill-rule="evenodd" d="M2 6c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v12c0 .6-.2 1-.6 1.4a1 1 0 0 1-.9.6H4a2 2 0 0 1-2-2V6Zm6.9 12 3.8-5.4-4-4.3a1 1 0 0 0-1.5.1L4 13V6h16v10l-3.3-3.7a1 1 0 0 0-1.5.1l-4 5.6H8.9Z" clip-rule="evenodd"/>
                                              </svg>
                                            
                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[18px]  h-[18px] text-gray-800 " viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                                    <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-256.000000, -671.000000)" fill="#000000">
                                                        <path d="M265,675 C264.448,675 264,675.448 264,676 C264,676.553 264.448,677 265,677 C265.552,677 266,676.553 266,676 C266,675.448 265.552,675 265,675 L265,675 Z M269,675 C268.448,675 268,675.448 268,676 C268,676.553 268.448,677 269,677 C269.552,677 270,676.553 270,676 C270,675.448 269.552,675 269,675 L269,675 Z M286,679 L258,679 L258,675 C258,673.896 258.896,673 260,673 L284,673 C285.104,673 286,673.896 286,675 L286,679 L286,679 Z M286,699 C286,700.104 285.104,701 284,701 L260,701 C258.896,701 258,700.104 258,699 L258,681 L286,681 L286,699 L286,699 Z M284,671 L260,671 C257.791,671 256,672.791 256,675 L256,699 C256,701.209 257.791,703 260,703 L284,703 C286.209,703 288,701.209 288,699 L288,675 C288,672.791 286.209,671 284,671 L284,671 Z M261,675 C260.448,675 260,675.448 260,676 C260,676.553 260.448,677 261,677 C261.552,677 262,676.553 262,676 C262,675.448 261.552,675 261,675 L261,675 Z" id="browser" sketch:type="MSShapeGroup">
                                            
                                            </path>
                                                    </g>
                                                </g>
                                            </svg>
                                        </a>
                                        <!-- ARROW -->
                                        <a href="#" class="text-gray-500 hover:text-gray-900  ">
                                            <svg class="w-[30px] h-[30px] text-gray-800 " viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.3891 8.11096L8.61091 15.8891" stroke="#333333" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.3891 8.11096L16.7426 12" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.3891 8.11096L12.5 7.75741" stroke="#333333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            
                                        </a>
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center text-base">
                                <h3 class="text-base font-normal text-gray-500 ">Status
                                </h3>
                                <span
                                    class=" text-base font-md font-bold text-gray-700 ">Complete</span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  ">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Created On</h3>
                                <span
                                    class="text-base font-md font-bold text-gray-700 ">30-Nov 1:35 AM</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- insert here -->
               
                <div class="p-4 bg-white">
                    <h1 class="px-4 text-xl font-semibold text-gray-900 sm:text-2xl py-0 ">Submission Summary</h1>
                    <div
                        class=" border-t border-gray-400 border-b grid w-full grid-cols-1  mt-4 xl:grid-cols-3 2xl:grid-cols-3">
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Num Started</h3>
                                <span
                                    class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl ">0</span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  border-r border-gray-400">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Num Submitted</h3>
                                <span
                                    class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl ">0</span>
                            </div>
                        </div>
                        <div class="items-center justify-between p-4 bg-white  sm:flex  ">
                            <div class="w-full text-center">
                                <h3 class="text-base font-normal text-gray-500 ">Num Unsubmitted</h3>
                                <span
                                    class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl ">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-4 items-center justify-between block sm:flex md:divide-x md:divide-gray-100 ">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="email" id="products-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 "
                                    placeholder="Search">
                            </div>
                        </form>
                    </div>
                    <div class="items-center sm:flex">
                        <div class="flex items-center">
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 uppercase focus:ring-blue-300 font-medium rounded-lg text-xs px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm ">
                    <!-- Table -->
                    <div class="flex flex-col mt-6">
                      <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                          <div class="overflow-hidden shadow sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 ">
                              <thead class="bg-gray-50 ">
                                <tr>
                                    <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                        <div class="flex items-center">
                                          Name
                                          <span class="ml-2">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                              <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                            </svg>
                                          </span>
                                        </div>
                                      </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Email
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Stated at
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Submitted at
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Duration
                                      <span class="ml-2">
                                        
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                    </button>
                                      </span>
                                    </div>
                                  </th>
                                  <th scope="col" class="p-4 text-xs font-bold tracking-wider text-left text-gray-500 ">
                                    <div class="flex items-center">
                                      Trust Score
                                      <span class="ml-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" id="arrow-circle-down" viewBox="0 0 24 24">
                                          <path d="M18.873,11.021H5.127a2.126,2.126,0,0,1-1.568-3.56L10.046.872a2.669,2.669,0,0,1,3.939.034l6.431,6.528a2.126,2.126,0,0,1-1.543,3.587ZM12,24.011a2.667,2.667,0,0,1-1.985-.887L3.584,16.6a2.125,2.125,0,0,1,1.543-3.586H18.873a2.125,2.125,0,0,1,1.568,3.558l-6.487,6.589A2.641,2.641,0,0,1,12,24.011Z"/>
                                        </svg>
                                      </span>
                                    </div>
                                  </th>
                                  
                                </tr>
                              </thead>
                              <tbody class="bg-white ">
                                <tr>
                                  <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                    <span class="font-semibold">Alvince Arandia</span>
                                  </td>
                                  <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                    vincearandia@gmail.com
                                  </td>
                                  <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                    8-Dec 0:59 AM
                                  </td>
                                  <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                    8-Dec 0:59 AM
                                  </td>
                                  <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                    30 minutes
                                  </td>
                                  <td class="p-4 whitespace-nowrap">
                                    <a
                                      class=" text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5">View Report</a>
                                  </td>
                                </tr>
                                <tr class="bg-gray-100 ">
                                    <td class="p-4 text-sm font-normal text-gray-900 whitespace-nowrap ">
                                      <span class="font-semibold">Alvince Arandia</span>
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                      renzidelposo@gmail.com
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap ">
                                      8-Dec 08:59 AM
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                      8-Dec 11:59 PM
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap ">
                                      30 minutes
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                      <a
                                        class=" text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5">View Report</a>
                                    </td>
                                  </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                        <!-- card footer -->
                        <div class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between d">
                            <!-- note: do not delete this haha -->
                            <div class="flex items-center mb-4 sm:mb-0">
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center mb-4 sm:mb-0">
                                    <!-- previous 1 -->
                                    <a href="#" class="inline-flex border justify-center p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7 transform -scale-x-1" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.8536 11.1464C13.0488 11.3417 13.0488 11.6583 12.8536 11.8536C12.6583 12.0488 12.3417 12.0488 12.1464 11.8536L8.14645 7.85355C7.95118 7.65829 7.95118 7.34171 8.14645 7.14645L12.1464 3.14645C12.3417 2.95118 12.6583 2.95118 12.8536 3.14645C13.0488 3.34171 13.0488 3.65829 12.8536 3.85355L9.20711 7.5L12.8536 11.1464ZM6.85355 11.1464C7.04882 11.3417 7.04882 11.6583 6.85355 11.8536C6.65829 12.0488 6.34171 12.0488 6.14645 11.8536L2.14645 7.85355C1.95118 7.65829 1.95118 7.34171 2.14645 7.14645L6.14645 3.14645C6.34171 2.95118 6.65829 2.95118 6.85355 3.14645C7.04882 3.34171 7.04882 3.65829 6.85355 3.85355L3.20711 7.5L6.85355 11.1464Z" fill="#6b7280" />
                                        </svg>

                                    </a>
                                    <!--  -->
                                    <!-- previous 2 -->
                                    <a href="#" class="inline-flex border justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                    <!--  -->
                                    <!-- next 1 -->
                                    <a href="#" class="inline-flex justify-center border  p-1 mr-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                    <!--  -->
                                    <!-- next 2 -->
                                    <a href="#" class="inline-flex justify-center border  p-1 mr-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-7 h-7" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.14645 11.1464C1.95118 11.3417 1.95118 11.6583 2.14645 11.8536C2.34171 12.0488 2.65829 12.0488 2.85355 11.8536L6.85355 7.85355C7.04882 7.65829 7.04882 7.34171 6.85355 7.14645L2.85355 3.14645C2.65829 2.95118 2.34171 2.95118 2.14645 3.14645C1.95118 3.34171 1.95118 3.65829 2.14645 3.85355L5.79289 7.5L2.14645 11.1464ZM8.14645 11.1464C7.95118 11.3417 7.95118 11.6583 8.14645 11.8536C8.34171 12.0488 8.65829 12.0488 8.85355 11.8536L12.8536 7.85355C13.0488 7.65829 13.0488 7.34171 12.8536 7.14645L8.85355 3.14645C8.65829 2.95118 8.34171 2.95118 8.14645 3.14645C7.95118 3.34171 7.95118 3.65829 8.14645 3.85355L11.7929 7.5L8.14645 11.1464Z" fill="#6b7280" />
                                        </svg>
                                    </a>
                                    <span class="text-sm font-normal text-gray-500 ">Page<span class="font-semibold text-gray-900 ">1 of 1 </span>| <span class="font-semibold text-gray-900 pr-1 ">Go to Page</span></span>
                                    <input type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-gray-500 focus:border-gray-500 block w-12  p-2.5  " placeholder="1">
                                </div>
                            </div>
                        </div>
                  </div>
            </main>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
        <script src="https://flowbite-admin-dashboard.vercel.app//app.bundle.js"></script>
</body>

</html>