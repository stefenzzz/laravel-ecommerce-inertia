<script setup>
import {onMounted, ref, computed, watch} from "vue";
import {emitter, SHOW_NOTIFICATION, SHOW_NOTIFICATION_DURATION} from "@/event-bus.js";

//refs
const type = ref('success');
const show = ref(false);
const flashMessage = ref('')
let timeout;

// Methods

const closeDelay = (delayTime = 3000)=>{
    if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            show.value = false;
        }, delayTime );
}


onMounted(()=>{

    emitter.on(SHOW_NOTIFICATION, ({type:result,message:msg,})=>{
        show.value = true;
        type.value = result;
        flashMessage.value = msg;

        closeDelay(1000);
    })

    emitter.on(SHOW_NOTIFICATION_DURATION, ( {type:typ , _message:msg, delay:duration} )=>{
        show.value = true;
        type.value = typ;
        flashMessage.value = msg;
        closeDelay(duration);

    });

});
</script>


<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div v-if="show" v-html="flashMessage"
        class="z-50 fixed top-[10%] left-1/2 -translate-x-1/2 text-white leading-6
        text-center py-4 px-4 md:px-8 rounded-lg shadow-md w-fit font-figtree"
        :class="[
        {'bg-emerald-500': type == 'success'},
        {'bg-red-500': type == 'warning'},
        {'bg-red-500': type == 'error'},
            ]">
        </div>
    </transition>
</template>
