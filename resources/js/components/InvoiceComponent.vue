<template>
  <div class="invoice-creator">
    <form @submit.prevent="submitInvoice">
      <div class="row g-4">
        <div class="col-lg-12">
          <div class="card elegant-card mb-4" style="z-index: 778 !important;">
            <div class="card-body p-4">
              <div class="row g-4">
                <div class="col-md-6">
                  <label class="form-label text-dark fw-semibold mb-2">
                    تاريخ الفاتورة <span class="text-danger">*</span>
                  </label>
                  <div class="input-wrapper">
                    <i class="ti ti-calendar input-icon"></i>
                    <input
                      v-model="invoice.invoice_date"
                      type="date"
                      required
                      class="form-control form-control-lg elegant-input"
                    >
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label text-dark fw-semibold mb-2">
                    الزبون <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <div class="position-relative flex-grow-1">
                      <i class="ti ti-user input-icon"></i>
                      <input
                        v-model="customerSearch"
                        @input="debouncedSearchCustomers"
                        @focus="handleCustomerFocus"
                        @keydown.enter.prevent="selectFirstCustomer"
                        @keydown.arrow-down.prevent="navigateCustomers(1)"
                        @keydown.arrow-up.prevent="navigateCustomers(-1)"
                        type="text"
                        class="form-control form-control-lg elegant-input pe-5"
                        placeholder="ابحث عن زبون (اكتب 3 أحرف على الأقل)..."
                        :disabled="isEdit"
                        :class="{ 'is-invalid': customerSearchError }"
                      >
                      <div class="position-absolute top-50 end-0 translate-middle-y me-2" v-if="searchingCustomers">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                      </div>

                      <div
                        v-if="!isEdit && showCustomerDropdown && (filteredCustomers.length > 0 || customerSearchError)"
                        class="dropdown-menu show w-100 customer-dropdown shadow-lg"
                        style="z-index: 777;"
                      >
                        <div v-if="searchingCustomers" class="dropdown-item text-center py-3">
                          <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                          جاري البحث...
                        </div>

                        <div v-else-if="filteredCustomers.length === 0 && customerSearch.length >= 3" class="dropdown-item text-center text-muted py-3">
                          <i class="ti ti-search-off me-2"></i>
                          لم يتم العثور على زبائن
                          <hr class="my-2">
                          <button
                            type="button"
                            @click="openAddCustomerWithSearch"
                            class="btn btn-sm btn-outline-success"
                          >
                            <i class="ti ti-plus me-1"></i>
                            إضافة "{{ customerSearch }}" كزبون جديد
                          </button>
                        </div>

                        <button
                          v-else
                          type="button"
                          v-for="(customer, index) in filteredCustomers"
                          :key="customer.id"
                          @click="selectCustomer(customer)"
                          class="dropdown-item d-flex justify-content-between align-items-center customer-item"
                          :class="{ 'active': index === selectedCustomerIndex }"
                        >
                          <div class="d-flex align-items-center">
                            <div class="customer-avatar">
                              {{ customer.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                              <div class="fw-semibold" v-html="highlightSearchTerm(customer.name)"></div>
                              <small class="text-muted" v-html="highlightSearchTerm(customer.phone)"></small>
                            </div>
                          </div>
                          <i class="ti ti-chevron-left text-muted"></i>
                        </button>

                        <div v-if="hasMoreCustomers" class="dropdown-item text-center">
                          <button
                            type="button"
                            @click="loadMoreCustomers"
                            class="btn btn-sm btn-outline-primary"
                            :disabled="loadingMoreCustomers"
                          >
                            <span v-if="loadingMoreCustomers" class="spinner-border spinner-border-sm me-1"></span>
                            {{ loadingMoreCustomers ? 'جاري التحميل...' : 'عرض المزيد' }}
                          </button>
                        </div>
                      </div>
                    </div>
                    <button
                      type="button"
                      @click="showAddCustomerModal = true"
                      class="btn btn-outline-success elegant-btn"
                      :disabled="isEdit"
                    >
                      <i class="ti ti-plus"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="isEdit" class="mt-3">
                <span class="badge bg-light text-muted">رقم الفاتورة: <strong>{{ invoiceNumber }}</strong></span>
              </div>
            </div>
          </div>

          <div class="card elegant-card mb-4">
            <div class="card-header bg-light border-0 py-3">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold" style="color: #b48b1e;">الخدمات</h5>
                <button
                  type="button"
                  @click="addService"
                  class="btn elegant-btn text-white"
                  style="background-color: #b48b1e; border-color: #b48b1e;"
                >
                  <i class="ti ti-plus me-2"></i>{{ isEdit ? 'إضافة سطر' : 'إضافة خدمة' }}
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover elegant-table mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center border-0 py-3">الخدمة</th>
                      <th class="text-center border-0 py-3" width="120">الكمية</th>
                      <th class="text-center border-0 py-3" width="150">السعر</th>
                      <th class="text-center border-0 py-3" width="150">المجموع</th>
                      <th class="text-center border-0 py-3" width="100">الإجراءات</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in invoice.items" :key="index" class="align-middle">
                      <td class="p-3">
                        <select
                          v-model="item.service_id"
                          @change="updateServicePrice(item)"
                          required
                          class="form-select elegant-input"
                        >
                          <option value="">اختر خدمة</option>
                          <option v-for="service in services" :key="service.id" :value="service.id">
                            {{ service.name }} - {{ formatCurrency(service.price) }}
                          </option>
                        </select>
                      </td>
                      <td class="p-3">
                        <input
                          v-model.number="item.quantity"
                          type="number"
                          min="1"
                          required
                          @input="calculateItemTotal(item)"
                          class="form-control elegant-input text-center"
                        >
                      </td>
                      <td class="p-3">
                        <input
                          v-model.number="item.unit_price"
                          type="number"
                          step="0.001"
                          min="0"
                          required
                          @input="calculateItemTotal(item)"
                          class="form-control elegant-input text-center"
                        >
                      </td>
                      <td class="text-center p-3">
                        <span class="amount-badge">{{ formatCurrency(item.total_price) }}</span>
                      </td>
                      <td class="text-center p-3">
                        <button
                          type="button"
                          @click="removeService(index)"
                          class="btn btn-sm btn-outline-danger rounded-circle"
                          title="حذف"
                        >
                          <i class="ti ti-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="invoice.items.length === 0">
                      <td colspan="5" class="text-center text-muted py-4">لا توجد عناصر، أضف خدمة على الأقل</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="row">
              <div class="col-md-6">
                <div class="card elegant-card">
                  <div class="card-body p-4">
                    <div class="row g-4">
                      <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold mb-2">ملاحظات</label>
                        <textarea
                          v-model="invoice.notes"
                          rows="4"
                          class="form-control elegant-input"
                          placeholder="أضف أي ملاحظات إضافية..."
                        ></textarea>
                      </div>
                      <div class="col-md-6">
                        <div class="row g-3">
                          <div class="col-12">
                            <label class="form-label text-dark fw-semibold mb-2">
                              <i class="ti ti-ticket me-1 text-warning"></i>
                              كود الكوبون
                            </label>
                            <div class="input-group">
                              <input
                                v-model="couponCode"
                                type="text"
                                class="form-control elegant-input"
                                :class="{ 'is-invalid': couponError, 'is-valid': appliedCoupon }"
                                placeholder="أدخل كود الكوبون"
                                @input="resetCoupon"
                                style="text-transform: uppercase;"
                                :disabled="isEdit && appliedCoupon"
                              >
                              <button
                                type="button"
                                @click="applyCoupon"
                                :disabled="!couponCode || loadingCoupon || (isEdit && appliedCoupon)"
                                class="btn elegant-btn"
                                style="background-color: #b48b1e; border-color: #b48b1e; color: white;"
                              >
                                <span v-if="loadingCoupon" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="ti ti-check me-1"></i>
                                {{ loadingCoupon ? 'جاري التحقق...' : 'تطبيق' }}
                              </button>
                            </div>

                            <div v-if="appliedCoupon" class="window.toastr.error window.toastr.error-success mt-2 py-2">
                              <i class="ti ti-check-circle me-2"></i>
                              <strong>{{ appliedCoupon.name }}</strong> - خصم {{ appliedCoupon.formatted_discount }}
                              <button v-if="!isEdit" type="button" @click="removeCoupon" class="btn btn-sm btn-outline-danger ms-2">
                                إلغاء
                              </button>
                            </div>

                            <div v-if="couponError" class="text-danger mt-1">
                              <small>{{ couponError }}</small>
                            </div>
                          </div>

                          <div class="col-12">
                            <label class="form-label text-dark fw-semibold mb-2">
                              <i class="ti ti-discount me-1 text-warning"></i>
                              التخفيض الإضافي
                            </label>
                            <div class="input-wrapper">
                              <i class="ti ti-minus input-icon"></i>
                              <input
                                v-model.number="invoice.discount"
                                type="number"
                                step="0.001"
                                min="0"
                                :max="subtotal"
                                @input="calculateTotals"
                                class="form-control elegant-input"
                                placeholder="تخفيض إضافي (اختياري)"
                              >
                            </div>
                          </div>
                          <div class="col-12">
                            <label class="form-label text-dark fw-semibold mb-2">
                              <i class="ti ti-cash me-1 text-success"></i>
                              المبلغ المدفوع
                            </label>
                            <div class="input-wrapper">
                              <i class="ti ti-currency-dollar input-icon"></i>
                              <input
                                v-model.number="invoice.paid_amount"
                                type="number"
                                step="0.001"
                                min="0"
                                :max="total"
                                @input="calculateTotals"
                                class="form-control elegant-input"
                              >
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card summary-card sticky-top" style="top: 20px;">
                  <div class="card-header border-0 py-3" style="background-color: #b48b1e;">
                    <h5 class="mb-0 text-white fw-bold">
                      <i class="ti ti-calculator me-2"></i>
                      ملخص الفاتورة
                    </h5>
                  </div>
                  <div class="card-body p-4">
                    <div class="summary-item">
                      <span class="summary-label">المجموع الفرعي:</span>
                      <span class="summary-value">{{ formatCurrency(subtotal) }}</span>
                    </div>

                    <div class="summary-item" v-if="appliedCoupon">
                      <span class="summary-label">كوبون {{ appliedCoupon.code }}:</span>
                      <span class="summary-value text-success">- {{ formatCurrency(appliedCoupon.discount_amount) }}</span>
                    </div>

                    <div class="summary-item" v-if="invoice.discount > 0">
                      <span class="summary-label">تخفيض إضافي:</span>
                      <span class="summary-value text-danger">- {{ formatCurrency(invoice.discount) }}</span>
                    </div>

                    <hr class="my-3">

                    <div class="summary-item total-item">
                      <span class="summary-label h5 mb-0">المجموع الكلي:</span>
                      <span class="summary-value h4 mb-0 fw-bold" style="color: #b48b1e;">{{ formatCurrency(total) }}</span>
                    </div>

                    <div class="summary-item" v-if="invoice.paid_amount > 0">
                      <span class="summary-label">المبلغ المدفوع:</span>
                      <span class="summary-value text-success">{{ formatCurrency(invoice.paid_amount) }}</span>
                    </div>

                    <div class="summary-item" v-if="remainingAmount > 0">
                      <span class="summary-label">المبلغ المتبقي:</span>
                      <span class="summary-value text-warning fw-bold">{{ formatCurrency(remainingAmount) }}</span>
                    </div>
                  </div>

                  <div class="card-footer bg-light border-0 p-4">
                    <button
                      type="submit"
                      :disabled="loading || invoice.items.length === 0 || !invoice.customer_id"
                      class="btn w-100 elegant-btn btn-lg text-white"
                      style="background-color: #b48b1e; border-color: #b48b1e;"
                    >
                      <i class="ti ti-device-floppy me-2"></i>
                      {{ loading ? 'جاري الحفظ...' : (isEdit ? 'حفظ التعديلات' : 'حفظ الفاتورة') }}
                    </button>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </form>

    <!-- Modal إضافة زبون -->
    <div class="modal fade" :class="{ show: showAddCustomerModal }" :style="{ display: showAddCustomerModal ? 'block' : 'none' }" tabindex="-1" v-if="showAddCustomerModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content elegant-modal">
          <div class="modal-header text-white border-0" style="background-color: #b48b1e;">
            <h5 class="modal-title text-white fw-bold">
              <i class="ti ti-user-plus me-2"></i>
              إضافة زبون جديد
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showAddCustomerModal = false"></button>
          </div>
          <form @submit.prevent="addNewCustomer">
            <div class="modal-body p-4">
              <div class="row g-4">
                <div class="col-md-6">
                  <label class="form-label text-dark fw-semibold mb-2">
                    اسم الزبون <span class="text-danger">*</span>
                  </label>
                  <div class="input-wrapper">
                    <i class="ti ti-user input-icon"></i>
                    <input
                      v-model="newCustomer.name"
                      type="text"
                      required
                      class="form-control elegant-input"
                      placeholder="أدخل اسم الزبون"
                    >
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label text-dark fw-semibold mb-2">
                    رقم الهاتف <span class="text-danger">*</span>
                  </label>
                  <div class="input-wrapper">
                    <i class="ti ti-phone input-icon"></i>
                    <input
                      v-model="newCustomer.phone"
                      type="text"
                      required
                      class="form-control elegant-input"
                      placeholder="أدخل رقم الهاتف"
                    >
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label text-dark fw-semibold mb-2">ملاحظات</label>
                  <textarea
                    v-model="newCustomer.notes"
                    rows="3"
                    class="form-control elegant-input"
                    placeholder="أضف ملاحظات إضافية (اختياري)"
                  ></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer border-0 p-4">
              <button type="button" class="btn btn-light me-2" @click="showAddCustomerModal = false">
                <i class="ti ti-x me-2"></i>
                إلغاء
              </button>
              <button type="submit" class="btn elegant-btn text-white" style="background-color: #b48b1e; border-color: #b48b1e;">
                <i class="ti ti-check me-2"></i>
                إضافة الزبون
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade" :class="{ show: showAddCustomerModal }" v-if="showAddCustomerModal"></div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'

export default {
  name: 'InvoiceComponent',
  props: {
    invoiceId: {
      type: [Number, String],
      default: null
    }
  },
  setup(props) {
    if (typeof window.axios === 'undefined') {
      console.error('axios is not available')
      return
    }
    const axios = window.axios

    const isEdit = computed(() => !!props.invoiceId)

    const loading = ref(false)
    const customers = ref([])
    const services = ref([])
    const showAddCustomerModal = ref(false)
    const customerSearch = ref('')
    const showCustomerDropdown = ref(false)
    const filteredCustomers = ref([])
    const nextInvoiceNumber = ref(1)
    const searchingCustomers = ref(false)
    const customerSearchError = ref('')
    const selectedCustomerIndex = ref(-1)
    const hasMoreCustomers = ref(false)
    const loadingMoreCustomers = ref(false)
    const currentPage = ref(1)

    const couponCode = ref('')
    const appliedCoupon = ref(null)
    const couponError = ref('')
    const loadingCoupon = ref(false)

    let searchTimeout = null

    const invoice = ref({
      customer_id: '',
      invoice_date: new Date().toISOString().split('T')[0],
      discount: 0,
      paid_amount: 0,
      notes: '',
      items: []
    })

    const newCustomer = ref({
      name: '',
      phone: '',
      notes: ''
    })

    const invoiceNumber = computed(() => {
      if (isEdit.value && invoice.value.invoice_number) return invoice.value.invoice_number
      const year = new Date().getFullYear()
      const paddedNumber = String(nextInvoiceNumber.value).padStart(4, '0')
      return `INV-${year}-${paddedNumber}`
    })

    const subtotal = computed(() => {
      return invoice.value.items.reduce((sum, item) => sum + (Number(item.total_price) || 0), 0)
    })

    const total = computed(() => {
      const couponDiscount = appliedCoupon.value ? Number(appliedCoupon.value.discount_amount) : 0
      const additionalDiscount = Number(invoice.value.discount) || 0
      return Math.max(0, subtotal.value - couponDiscount - additionalDiscount)
    })

    const remainingAmount = computed(() => {
      return Math.max(0, total.value - (Number(invoice.value.paid_amount) || 0))
    })

    watch(customerSearch, (newValue) => {
      selectedCustomerIndex.value = -1
      customerSearchError.value = ''
      if (newValue.length === 0 && !isEdit.value) {
        showCustomerDropdown.value = false
        invoice.value.customer_id = ''
        filteredCustomers.value = []
      }
    })

    const loadData = async () => {
      try {
        const promises = [axios.get('/api/services')]
        if (!isEdit.value) promises.push(axios.get('/api/next-invoice-number'))
        const [servicesRes, nextNumRes] = await Promise.all(promises)
        services.value = servicesRes.data
        if (!isEdit.value) {
          nextInvoiceNumber.value = nextNumRes?.data?.next_number || 1
        } else {
          await loadInvoice()
        }
      } catch (e) {
        console.error(e)
        if (!isEdit.value) nextInvoiceNumber.value = 1
      }
    }

    const loadInvoice = async () => {
      const res = await axios.get(`/api/invoices/${props.invoiceId}`)
      const data = res.data
      invoice.value.customer_id   = data.customer_id
      invoice.value.invoice_date  = data.invoice_date
      invoice.value.discount      = data.discount ?? 0
      invoice.value.paid_amount   = data.paid_amount ?? 0
      invoice.value.notes         = data.notes ?? ''
      invoice.value.invoice_number = data.invoice_number
      invoice.value.items = (data.items || []).map(it => ({
        service_id: it.service_id,
        quantity: Number(it.quantity),
        unit_price: Number(it.unit_price),
        total_price: Number(it.total_price)
      }))
      if (data.customer) {
        customerSearch.value = `${data.customer.name} - ${data.customer.phone ?? ''}`.trim()
      }
      if (data.coupon) {
        appliedCoupon.value = {
          id: data.coupon.id,
          code: data.coupon.code,
          name: data.coupon.name,
          formatted_discount: data.coupon.formatted_discount,
          discount_amount: Number(data.coupon.discount_amount || 0)
        }
        couponCode.value = data.coupon.code
      }
    }

    const debouncedSearchCustomers = () => {
      if (isEdit.value) return
      if (searchTimeout) clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        searchCustomers()
      }, 300)
    }

    const searchCustomers = async () => {
      if (isEdit.value) return
      const searchTerm = customerSearch.value.trim()
      if (searchTerm.length < 3) {
        showCustomerDropdown.value = false
        return
      }
      searchingCustomers.value = true
      customerSearchError.value = ''
      currentPage.value = 1
      try {
        const response = await axios.get('/api/customers/search', {
          params: { q: searchTerm, page: 1, per_page: 10 }
        })
        filteredCustomers.value = response.data.data || response.data
        hasMoreCustomers.value = response.data.has_more_pages || false
        showCustomerDropdown.value = true
      } catch (error) {
        console.error('Search error:', error)
        customerSearchError.value = 'حدث خطأ أثناء البحث'
        filteredCustomers.value = []
        showCustomerDropdown.value = true
      } finally {
        searchingCustomers.value = false
      }
    }

    const loadMoreCustomers = async () => {
      if (isEdit.value || loadingMoreCustomers.value || !hasMoreCustomers.value) return
      loadingMoreCustomers.value = true
      currentPage.value += 1
      try {
        const response = await axios.get('/api/customers/search', {
          params: { q: customerSearch.value.trim(), page: currentPage.value, per_page: 10 }
        })
        const newCustomers = response.data.data || response.data
        filteredCustomers.value = [...filteredCustomers.value, ...newCustomers]
        hasMoreCustomers.value = response.data.has_more_pages || false
      } catch (error) {
        console.error('Load more error:', error)
        currentPage.value -= 1
      } finally {
        loadingMoreCustomers.value = false
      }
    }

    const handleCustomerFocus = () => {
      if (isEdit.value) return
      if (customerSearch.value.length >= 3 && filteredCustomers.value.length > 0) {
        showCustomerDropdown.value = true
      }
    }

    const navigateCustomers = (direction) => {
      if (isEdit.value) return
      if (!showCustomerDropdown.value || filteredCustomers.value.length === 0) return
      const maxIndex = filteredCustomers.value.length - 1
      selectedCustomerIndex.value += direction
      if (selectedCustomerIndex.value > maxIndex) selectedCustomerIndex.value = 0
      else if (selectedCustomerIndex.value < 0) selectedCustomerIndex.value = maxIndex
    }

    const selectFirstCustomer = () => {
      if (isEdit.value) return
      if (filteredCustomers.value.length > 0) {
        const customerToSelect = selectedCustomerIndex.value >= 0
          ? filteredCustomers.value[selectedCustomerIndex.value]
          : filteredCustomers.value[0]
        selectCustomer(customerToSelect)
      }
    }

    const highlightSearchTerm = (text) => {
      if (!customerSearch.value.trim() || !text) return text
      const searchTerm = customerSearch.value.trim().toLowerCase()
      const textLower = String(text).toLowerCase()
      const index = textLower.indexOf(searchTerm)
      if (index === -1) return text
      return text.substring(0, index) +
             '<mark class="bg-warning">' +
             text.substring(index, index + searchTerm.length) +
             '</mark>' +
             text.substring(index + searchTerm.length)
    }

    const openAddCustomerWithSearch = () => {
      if (isEdit.value) return
      newCustomer.value.name = customerSearch.value.trim()
      showAddCustomerModal.value = true
      showCustomerDropdown.value = false
    }

    const selectCustomer = (customer) => {
      invoice.value.customer_id = customer.id
      customerSearch.value = `${customer.name} - ${customer.phone}`
      showCustomerDropdown.value = false
    }

    const addService = () => {
      invoice.value.items.push({
        service_id: '',
        quantity: 1,
        unit_price: 0,
        total_price: 0
      })
    }

    const removeService = (index) => {
      invoice.value.items.splice(index, 1)
    }

    const updateServicePrice = (item) => {
      const service = services.value.find(s => s.id == item.service_id)
      if (service) {
        item.unit_price = parseFloat(service.price)
        calculateItemTotal(item)
      }
    }

    const calculateItemTotal = (item) => {
      item.total_price = (Number(item.quantity) || 0) * (Number(item.unit_price) || 0)
    }

    const calculateTotals = () => {}

    const resetCoupon = () => {
      if (couponError.value) couponError.value = ''
    }

    const applyCoupon = async () => {
      if (!couponCode.value.trim()) return
      const code = couponCode.value.trim().toUpperCase()
      loadingCoupon.value = true
      couponError.value = ''
      try {
        const response = await axios.post('/api/validate-coupon', {
          code: code,
          amount: subtotal.value
        })
        if (response.data.valid) {
          appliedCoupon.value = response.data.coupon
          couponCode.value = code
          couponError.value = ''
        }
      } catch (error) {
        couponError.value = error.response?.data?.message || 'حدث خطأ أثناء التحقق من الكوبون'
        appliedCoupon.value = null
      } finally {
        loadingCoupon.value = false
      }
    }

    const removeCoupon = () => {
      appliedCoupon.value = null
      couponCode.value = ''
      couponError.value = ''
    }

    const addNewCustomer = async () => {
      if (!newCustomer.value.name.trim() || !newCustomer.value.phone.trim()) {
        window.toastr.error('يرجى إدخال اسم الزبون ورقم الهاتف')
        return
      }
      try {
        const response = await axios.post('/api/customers', newCustomer.value)
        if (response.data.success) {
          customers.value.push(response.data.customer)
          selectCustomer(response.data.customer)
          newCustomer.value = { name: '', phone: '', notes: '' }
          showAddCustomerModal.value = false
          window.toastr.success('تم إضافة الزبون بنجاح')
        }
      } catch (error) {
        console.error('Error adding customer:', error)
        window.toastr.error('حدث خطأ أثناء إضافة الزبون')
      }
    }

    const submitInvoice = async () => {
      if (invoice.value.items.length === 0) {
        window.toastr.error('يجب إضافة خدمة واحدة على الأقل')
        return
      }
      if (!invoice.value.customer_id) {
        window.toastr.error('يجب اختيار زبون')
        return
      }
      loading.value = true
      try {
        const dataToSend = {
          ...invoice.value,
          invoice_number: invoiceNumber.value,
          coupon_id: appliedCoupon.value ? appliedCoupon.value.id : null,
          coupon_code: appliedCoupon.value ? appliedCoupon.value.code : null,
          coupon_discount: appliedCoupon.value ? appliedCoupon.value.discount_amount : 0
        }
        let response
        if (isEdit.value) {
          response = await axios.put(`/invoices/${props.invoiceId}`, dataToSend)
        } else {
          response = await axios.post('/invoices', dataToSend)
        }
        if (response.data.success) {
          window.toastr.success(isEdit.value ? 'تم تحديث الفاتورة بنجاح' : 'تم إنشاء الفاتورة بنجاح')
          window.location.href = '/invoices'
        }
      } catch (error) {
        console.error('Full error:', error)
        window.toastr.error(isEdit.value ? 'حدث خطأ أثناء تحديث الفاتورة' : 'حدث خطأ أثناء إنشاء الفاتورة')
      } finally {
        loading.value = false
      }
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('ar-LY', {
        style: 'decimal',
        minimumFractionDigits: 3,
        maximumFractionDigits: 3
      }).format(Number(amount) || 0) + ' د.ل'
    }

    const handleClickOutside = (event) => {
      if (!event.target.closest('.position-relative')) {
        showCustomerDropdown.value = false
      }
    }

    onMounted(() => {
      loadData()
      if (!isEdit.value) addService()
      document.addEventListener('click', handleClickOutside)
    })

    return {
      isEdit,
      loading,
      customers,
      services,
      showAddCustomerModal,
      customerSearch,
      showCustomerDropdown,
      filteredCustomers,
      searchingCustomers,
      customerSearchError,
      selectedCustomerIndex,
      hasMoreCustomers,
      loadingMoreCustomers,
      couponCode,
      appliedCoupon,
      couponError,
      loadingCoupon,
      invoice,
      newCustomer,
      invoiceNumber,
      subtotal,
      total,
      remainingAmount,
      debouncedSearchCustomers,
      searchCustomers,
      loadMoreCustomers,
      handleCustomerFocus,
      navigateCustomers,
      selectFirstCustomer,
      highlightSearchTerm,
      openAddCustomerWithSearch,
      selectCustomer,
      addService,
      removeService,
      updateServicePrice,
      calculateItemTotal,
      calculateTotals,
      resetCoupon,
      applyCoupon,
      removeCoupon,
      addNewCustomer,
      submitInvoice,
      formatCurrency
    }
  }
}
</script>


<style scoped>
.invoice-creator {
  max-width: 1400px;
  margin: 0 auto;
  background: #f8fafc;
  padding: 20px;
  border-radius: 20px;
}

/* Header Card */
.header-card {
  background: #b48b1e;
  color: white;
  border: none;
  border-radius: 15px;
}

.invoice-number-badge {
  background: #e8c9bf;
  color: #b48b1e;
  padding: 12px 24px;
  border-radius: 25px;
  font-size: 1.1rem;
  font-weight: 600;
  display: inline-block;
}

/* Elegant Cards */
.elegant-card {
  border: none;
  border-radius: 15px;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.elegant-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
}

/* Input Styling */
.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  z-index: 10;
}

.elegant-input {
  border: 2px solid #e9ecef;
  border-radius: 12px;
  padding: 12px 45px 12px 15px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #fff;
}

.elegant-input:focus {
  border-color: #b48b1e;
  box-shadow: 0 0 0 3px rgba(180, 139, 30, 0.1);
  background: #fff;
}

/* Button Styling */
.elegant-btn {
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 600;
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.elegant-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Table Styling */
.elegant-table {
  border: none;
}

.elegant-table thead th {
  background: #f8f9fa;
  color: #495057;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.875rem;
  letter-spacing: 0.5px;
}

.elegant-table tbody tr {
  border-bottom: 1px solid #f1f3f4;
  transition: all 0.2s ease;
}

.elegant-table tbody tr:hover {
  background: rgba(180, 139, 30, 0.04);
}

/* Amount Badge */
.amount-badge {
  background: #b48b1e;
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.9rem;
}

/* Customer Dropdown */
.customer-dropdown {
  position: absolute;
  top: 100%;
  z-index: 1050;
  max-height: 320px;
  overflow-y: auto;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  border: none;
  border-radius: 15px;
  padding: 8px 0;
}

.customer-item {
  padding: 12px 20px;
  border: none;
  transition: all 0.2s ease;
  border-radius: 0;
}

.customer-item:hover,
.customer-item.active {
  background: #e8c9bf;
  border-right: 4px solid #b48b1e;
}

.customer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #b48b1e;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
  margin-left: 12px;
}

/* Summary Card */
.summary-card {
  border: none;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f1f3f4;
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-label {
  color: #6c757d;
  font-weight: 500;
}

.summary-value {
  font-weight: 700;
  font-family: 'Courier New', monospace;
}

.total-item {
  background: #e8c9bf;
  padding: 16px;
  margin: 0 -16px;
  border-radius: 10px;
  border: none !important;
}

/* Modal Styling */
.elegant-modal {
  border: none;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.elegant-modal .modal-header {
  border: none;
  padding: 24px 24px 12px;
}

.elegant-modal .modal-body {
  padding: 12px 24px;
}

.elegant-modal .modal-footer {
  border: none;
  padding: 12px 24px 24px;
}

/* Responsive Design */
@media (max-width: 992px) {
  .invoice-creator {
    padding: 15px;
  }
  
  .summary-card {
    position: static !important;
  }
  
  .elegant-input {
    padding: 10px 40px 10px 12px;
  }
}

@media (max-width: 768px) {
  .invoice-creator {
    padding: 10px;
    margin: 10px;
  }
  
  .header-card .row {
    text-align: center;
  }
  
  .header-card .col-md-6:last-child {
    margin-top: 15px;
  }
  
  .elegant-table {
    font-size: 0.875rem;
  }
  
  .customer-avatar {
    width: 35px;
    height: 35px;
    font-size: 0.875rem;
  }
}

/* Animation for smooth interactions */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.elegant-card {
  animation: fadeInUp 0.6s ease-out;
}

/* Loading states */
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Smooth scrolling for dropdown */
.customer-dropdown {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.customer-dropdown::-webkit-scrollbar {
  width: 6px;
}

.customer-dropdown::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 10px;
}

.customer-dropdown::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}

.customer-dropdown::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>