<template>
  <div>
    <div class="timeline" v-loading="loading">
      <div class="item" v-for="item in timeline" :key="item.id" v-on:click="itemClick(item.id)">
        <div class="circle"></div>
        <div v-if="item.user">{{ item.user.nickname }} &lt;{{ item.user.email }}&gt;</div>
        <div>Updated: {{ item.title }}</div>
        <div class="time">{{ (new Date(item.updated * 1000)).toLocaleString() }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      loading: false,
      timeline: []
    }
  },
  mounted () {
    this.loadTimeline()
  },
  methods: {
    loadTimeline () {
      this.loading = true
      api('timeline/fetch', {}).then(data => {
        this.timeline = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      }).then(() => this.loading = false)
    },
    itemClick (id) {
      this.$router.push(`/timeline/${id}`)
    }
  }
}
</script>

<style lang="less" scoped>
.timeline {
  border-left: 3px solid #ddd;
  margin: 0 0 0 20px;
  min-height: 50px;

  .item {
    padding: 10px;
    font-size: 14px;

    .circle {
      width: 10px;
      height: 10px;
      background-color: whitesmoke;
      position: absolute;
      margin-left: -18px;
      margin-top: 4px;
      border: #6e0f6d solid 1.5px;
      border-radius: 50%;
    }

    .time {
      font-size: 12px;
      color: #aaa;
    }
  }

  .item:hover {
    cursor: pointer;
    background-color: rgba(110, 15, 109, 0.1);
  }
}
</style>
