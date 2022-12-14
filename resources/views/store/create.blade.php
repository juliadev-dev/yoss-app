@include('assets.header')

@include('assets.sidebar')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Create Store</h1>
    </div>
    <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
          <div class="card-body">
            <form action="{{ url('store') }}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="form-group col-md-6 col-lg-6">
                <label class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group col-md-6 col-lg-6">
                <label>Phone</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone">
                @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group col-md-6 col-lg-6">
                <label>Address</label>
                <textarea type="text" class="form-control @error('address') is-invalid @enderror" name="address"></textarea>
                @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group col-md-6 col-lg-6">
                <button type="submit" class="btn btn-primary text-right">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@include('assets.footer')