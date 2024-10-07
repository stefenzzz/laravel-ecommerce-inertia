
<script setup>

import { computed, watch, ref } from 'vue';

  const model = defineModel({
      type: [String, Number, File, Boolean],
      default: '', 
    });
  const props = defineProps({
    id: Number,
    label: String,
    placeholder: String,
    required: Boolean,
    type: {
      type: String,
      default: 'text'
    },
    name: String,
    selectOptions: Array,
    rows: String,
    customClass: String,
    message: String
  })

const msg = ref('');

// create unique id if input id is not set
const id = computed(() => {
  if (props.id) return props.id;
  return `id-${Math.floor(1000000 + Math.random() * 1000000)}`;
})

const emit = defineEmits(['update:modelValue', 'change'])

const onChange = (value)=>{
  emit('update:modelValue', value)
  emit('change', value)
}

watch(()=>props.message,(message)=> msg.value = message);

</script>
  


<template>
    
    <div>
        <label class="sr-only">{{ label || placeholder }}</label>
        <template v-if="type === 'select'">
          <select :name="name"
                  :required="required"
                  :value="model"
                  :class="[customClass,'custom-input']"
                  @change="onChange($event.target.value)">
            <option value="default">{{ placeholder}}</option>
            <option v-for="option of selectOptions" :value="option.key">{{option.text}}</option>
          </select>
        </template>
        <template v-else-if="type === 'textarea'">
        <textarea :name="name"
                  :required="required"
                  :value="model"
                  @input="emit('update:modelValue', $event.target.value)"
                  :class="[customClass,'custom-input']"
                  :placeholder="label || placeholder"
                  :rows="rows"
                  @focus="msg = '' "
                  ></textarea>
        </template>
        <template v-else-if="type === 'file'">
          <input :type="type"
                 :name="name"
                 :required="required"
                 :value="model"
                 @input="emit('change', $event.target.files[0])"
                 :class="[customClass,'custom-input']"
                 :placeholder="label || placeholder"/>
        </template>
        <template v-else-if="type === 'checkbox'">
            <div class="flex">
                <input :id="id"
                  :name="name"
                  :type="type"
                  :checked="model"
                  :required="required"
                  @change="emit('update:modelValue', $event.target.checked)"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"/>
            <label :for="id" class="ml-2 block text-sm text-gray-900"> {{ label }} </label>
            </div>
        </template>
        <template v-else-if=" type === 'number' ">
          <input :type="type"
                 :name="name"
                 :required="required"
                 :value="model"
                 @input="emit('update:modelValue', $event.target.value)"
                 :class="[customClass,'custom-input']"
                 :placeholder="label || placeholder"
                 @focus="msg = '' "/>          
        </template>
        <template v-else>
          <input :type="type"
                 :name="name"
                 :required="required"
                 :value="model"
                 @input="emit('update:modelValue', $event.target.value)"
                 :class="[customClass,'custom-input']"
                 :placeholder="label || placeholder"
                 @focus="msg = '' "/>
        </template>
        <span v-if="msg" class="text-sm text-red-600">{{ msg }}</span>
    </div>
  </template>
  
