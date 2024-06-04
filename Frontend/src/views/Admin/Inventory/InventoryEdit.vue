<script>
import axios from 'axios';

export default {
  name: 'EditInventoryItem',
  data() {
    return {
      itemId: '',
      errorList: {},
      model: {
        item: {
          price: '',
          amount: '',
        },
      },
    };
  },

  mounted() {
    this.itemId = this.$route.params.id;
    this.getItemData(this.itemId);
  },

  methods: {
    getItemData(itemId) {
      axios.get(`/items/${itemId}/edit`)
        .then((res) => {
          console.log('Item data response:', res.data);
          if (res.data.items) {
            this.model.item = res.data.items;
          } else {
            console.error('Item data not found in response');
          }
        })
        .catch((error) => {
          console.error('Error fetching item data:', error);
          if (error.response && error.response.status === 404) {
            this.$router.push('/admin/items');
          }
        });
    },

    async updateItem() {
      const data = {
        price: this.model.item.price,
        amount: this.model.item.amount,
      };

      try {
        const res = await axios.put(`/items/${this.itemId}/update-inventory`, data, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });
        const successMessage = res.data.message;
        this.$router.push('/admin/items?successMessage=' + successMessage);
        this.errorList = {};
      } catch (error) {
        console.error('Error updating item:', error);
        if (error.response && error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else if (error.request) {
          console.log(error.request);
        } else {
          console.log('Error', error.message);
        }
      }
    },

    Exit() {
      this.$router.push('/admin/inventory');
    },
  },
};
</script>

<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Edit Inventory Item</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="Price">Price</label>
          <input v-model="model.item.price" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Amount">Amount</label>
          <input v-model="model.item.amount" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="updateItem">Update</button>
          <button class="btn btn-danger" type="button" @click="Exit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
}

.card {
  margin-top: 20px;
}

.mb-3 {
  margin-bottom: 15px;
}
</style>
