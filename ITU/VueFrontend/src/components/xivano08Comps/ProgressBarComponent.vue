<!-- author - xivano08 -->

<template>
    <div style="max-width: 400px; margin: 0 auto; text-align: center;">
      <div style="width: 100%; height: 30px; background-color: #f3f3f3; border-radius: 5px; margin-bottom: 20px;">
        <div class="progress-bar" :style="{ width: progress + '%' }">
            <h4>{{ progress + '%' }}</h4>
        </div>
      </div>

    </div>
  </template>
  
  <script>
  export default {
    props: {
        progress: {
          type: Number,
          required: true,
          default: 0,
        },
    }
  };
  </script>
  
  <style scoped>

  .progress-bar {
    height: 100%;
    background-color: #4caf50;
    border-radius: 5px;
    transition: width 0.3s ease;
  }


  </style>