// post-item component

//requires the following libraries to be installed globally by wp
// add the following to functions.php
//    wp_register_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js');

//    wp_register_script('wpvue_vuejs', 'https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.min.js');
//    wp_register_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js');
    // wp_register_script('axios', 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js');



Vue.component('post-item', {
  data: function () {
    return {
    }
  },
  props: ['post', 'categories'],
  computed: {
    backgroundImage() {
      return 'background-image: url('+this.post.fimg_url+'); background-size: cover; background-repeat: no-repeat; background-position: center;';
    },
  },
  methods: {
    lookupCategory: function(categoryID) {
      // var categories = this.categories;
      // categoryName = categories.find(cat => cat.id == categoryID ); 
      // console.log('categoryName = ' + categoryName)
      // if (categoryName === undefined) { return "UNKNOWN" } else { return categoryName.toUpperCase() } // return category.name.toUpperCase() } 
      return "ERROR";
    },
    formatDate: function(date, formatIn, formatOut) {
      return moment(date, formatIn).format(formatOut) 
    }

  },
  template: `

      <div class="gridpostitem" :style="backgroundImage">
      <a :href="post.link" rel="bookmark">
      <img src="/wp-content/uploads/2020/04/blank.png" width="100%" height="100%"></a>
        <div class="articlecategory"><div class="categorypadding bodyfont">{{ lookupCategory(this.post.categories[0]) }}</div></div>
        <div class="articledate">
          <div class="articlemonth bodyfont">{{ formatDate(post.date, 'YYYY-MM-DDTHH:mm:ss', 'MMM') }}</div>
          <div class="articleday titlefont">{{ formatDate(post.date, 'YYYY-MM-DDTHH:mm:ss', 'DD') }}</div>
          <div class="articleyear bodyfont">{{ formatDate(post.date, 'YYYY-MM-DDTHH:mm:ss', 'YYYY') }}</div>  
        </div>
        <div class="articletitle"><div class="titlepadding bodyfont">{{ post.title.rendered }}</div></div>
      </div>     
  `
})

// post-grid component
Vue.component('post-grid', {
  template: `
  <div>
<h1>Updated 1</h1>
    <div class="searchfilter">
      Search: <input v-model="searchPosts" type="text" />
      Date Filter: 
        <select v-model="searchYear">
          <option value="all">All</option>
          <option value="2020">2020</option>
          <option value="2019">2019</option>
        </select>
    </div>

    Search Year {{ searchYear }}
    

      <transition-group tag="div" class="gridposts" name="fade-list">
        <post-item v-for="post in filteredPosts" :key="post.id" :post="post" :categories="categories" />
      </transition-group>
  
    </div>
  `,
  mounted() {
    this.getPosts();
  },
  data() {
    return {
      searchYear: '2020',
      searchPosts: '',
      message: 'Vue is Working',
      categories: [
        { id: '10', name:'News', slug: 'news'},
        { id: '11', name:'Featured', slug: 'featured'},
      ],
      posts: [],
      postsURL: 'http://178.62.47.158/wp-json/wp/v2/posts', //
      postsData: {
        per_page: 10,
        page:  1
      }

    }
  },
  computed: {
    filteredPosts() {
      var posts = this.posts;
      if (posts.length !== 0) {
      if (this.searchYear !== 'all') {
        console.log("searchYear ==" + this.searchYear)
        posts = posts.filter((post) => moment(post.date, 'YYYY-MM-DDTHH:mm:ss').format('YYYY') === this.searchYear);
      } else {
        console.log("searchYear ==" + this.searchYear)
      }
      posts = posts.filter((data) =>  JSON.stringify(data).toLowerCase().indexOf(this.searchPosts.toLowerCase()) !== -1);

    }

      return posts;
    }
  },
  methods: {
    getPosts() {
      axios.get(this.postsURL, {params: this.postsData})
      .then((response) => { 
        this.posts = response.data
      })
      .catch ((error) => {
        console.log(error); 
      })
    }
  }  
})

var app = new Vue({
  el: '#divWpVue',
})