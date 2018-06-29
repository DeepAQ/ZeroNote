<template>
  <div id="page">
    <h3><i class="el-icon-search"></i> Search results for "{{kw}}"</h3>
    <div class="item" v-for="note in results">
      <div class="title">
        <i class="el-icon-document"></i>
        <router-link :to="{ name: 'read', params: { id: note.id }}">
          {{ note.title }}
        </router-link>
      </div>
      <div class="info">
        <i class="el-icon-star-on"></i> {{ note.upvotes }}&nbsp;&nbsp;
        <i class="el-icon-time"></i> {{ (new Date(note.updated * 1000)).toLocaleString() }}
      </div>
      <div class="preview">
        {{ note.preview }} ……
      </div>
    </div>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  props: ['kw'],
  data () {
    return {
      results: []
    }
  },
  watch: {
    kw () {
      this.doSearch()
    }
  },
  methods: {
    doSearch () {
      if (this.kw != '') {
        api('hub/search', {
          kw: this.kw
        }).then(data => {
          this.results = data
        }).catch(reason => {
          this.$message({
            showClose: true,
            type: 'error',
            message: reason
          })
        })
      } else {
        this.$router.replace('/')
      }
    }
  },
  mounted () {
    this.doSearch()
  }
}
</script>

<style lang="less" scoped>
#page {
  width: 100%;

  h3 {
    margin: 10px 0;
  }

  .item {
    padding-top: 10px;
    margin-bottom: 10px;
    border-top: solid 1px #ccc;

    .title {
      font-size: 20px;
      color: #6e0f6d;

      a {
        color: #6e0f6d;
        text-decoration: none;
      }
    }

    .info {
      font-size: 14px;
      color: #aaa;
    }

    .preview {
      font-size: 12px;
      color: #666;
    }
  }
}
</style>
