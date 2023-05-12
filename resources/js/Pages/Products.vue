<template>
    <div>
        <Menu mode="horizontal">
            <MenuItem key="product">
                Products
            </MenuItem>
            <MenuItem key="graph">
                Graph
            </MenuItem>
        </Menu>
        <Card>
            <Space direction="vertical" :size="8">
                <InputSearch
                    v-model:value="search"
                    placeholder="Search Title or SKU"
                    enter-button
                    @search="onSearch"
                    :loading="searching"
                />

                <Table :dataSource="products.data" :columns="columns" :pagination="false" >
                    <template #bodyCell="{ column, record }" >
                        <template v-if="column.key === 'image'">
                            <Image
                                :width="50"
                                :src="record.image"
                            />
                        </template>
                        <template v-if="column.key === 'title'" >
                            <div class="product-title">
                                {{ record.title }}
                                <Button warning class="edit-button" @click="openUpdateModal(record)">
                                    Edit <edit-two-tone />
                                </Button>
                            </div>
                        </template>
                    </template>
                </Table>

                <Pagination
                    v-model:current="currentPage"
                    v-model:page-size="perPage"
                    :total="products.total"
                    :show-total="total => `Total ${total} items`"
                    @change="changePage"
                />
            </Space>
        </Card>

        <Modal
            v-model:visible="editModal"
            title="Update Product"
            centered
        >
        <Form layout="vertical" :model="productForm" ref="formRef">
                <FormItem
                    label="Title"
                    name="title"
                    :rules="[{ required: true }]"
                >
                    <Input v-model:value="productForm.title"></Input>
                </FormItem>
                <FormItem
                    label="Ingredients"
                    name="ingredients"
                >
                    <Textarea v-model:value="productForm.ingredients"></Textarea>
                </FormItem>
                <FormItem
                    label="Quantity"
                    name="quantity"
                    :rules="[{ required: true }]"
                >
                    <InputNumber v-model:value="productForm.quantity"></InputNumber>
                </FormItem>

            </Form>
            <template #footer>
                <Button key="cancel" @click="cancel">Cancel</Button>
                <Button key="save" type="primary" @click="updateProduct" :loading="isSaving">Save</Button>
            </template>
        </Modal>
    </div>
</template>
<script setup>
import { Menu, MenuItem, Table, Image, Button, Modal, Form, FormItem, Input, Textarea, InputNumber, InputSearch, Space, Card, Pagination } from 'ant-design-vue'
import { EditTwoTone } from '@ant-design/icons-vue'
import { ref, reactive } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

// products
const props = defineProps({ products: Object })

// table
const columns = ref([
  { title: 'Image', dataIndex: 'image', key: 'image' },
  { title: 'Title', dataIndex: 'title', key: 'title' },
  { title: 'Ingredients', dataIndex: 'ingredients', key: 'ingredients' },
  { title: 'Price', dataIndex: 'price', key: 'price' },
  { title: 'SKU', dataIndex: 'sku', key: 'sku' },
  { title: 'Quantity', dataIndex: 'quantity', key: 'quantity' }
])

// edit
const editModal = ref(false)
const isSaving = ref(false)

const formRef = ref()
const productForm = useForm({
  _id: '',
  title: '',
  ingredients: '',
  quantity: ''
})

const updateProduct = async () => {
  isSaving.value = true
  try {
    await formRef.value.validateFields()
    productForm.put(`/product/${productForm._id}`, {
      ...productForm,
      onError: errors => {
        isSaving.value = false
      },
      onSuccess: () => {
        isSaving.value = false
        editModal.value = false
      }
    })
    // isSaving.value = false
  } catch (e) {
    isSaving.value = false
  }
}

const openUpdateModal = (productRecord) => {
  editModal.value = true
  productForm._id = productRecord._id
  productForm.title = productRecord.title
  productForm.ingredients = productRecord.ingredients
  productForm.quantity = productRecord.quantity
}

const cancel = () => {
  productForm.cancel()
  editModal.value = false
}

// search
const search = ref()
const searching = ref(false)
const onSearch = (searchValue) => {
  router.get('/', { search: searchValue }, {
    replace: true,
    onStart: () => {
      searching.value = true
    },
    onFinish: () => {
      searching.value = false
    }
  })
}

// pagination
const perPage = ref(props.products.per_page)
const currentPage = ref(props.products.current_page)

const changePage = async () => {
  router.get('/', { search: search.value, page: currentPage.value, per_page: perPage.value }, { only: ['products'] })
}
</script>
<style lang="scss" scope>
.edit-button {
    display: none;
}

.product-title:hover {
    .edit-button {
        display: block !important;
    }
}
</style>
