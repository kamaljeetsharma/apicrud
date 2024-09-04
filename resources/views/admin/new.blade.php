@extends('new')


@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">View profile</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">My Profile</li>
              </ol>
            </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  Profile
                </h3>
              </div><!-- /.card-header -->

<!--register form-->
<div class="card-body">
  <div class="tab-content p-0">
    <div class="media d-flex flex-column align-items-center">
      <img src="admin/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-100 img-circle">
      <button type="button" class="btn btn-primary mt-3">Change</button>
    </div>
                
    <form method="POST"enctype="multipart/form-data">
      @csrf
      <div class="form-group">
         <label for="name">First name</label>
         <input type="text" class="form-control" id="name" name="name" required>
      </div>
   
      <div class="form-group">
         <label for="lastname">Lastname</label>
         <input type="text" class="form-control" id="lastname" name="lastname" required>
      </div>
   
      <div class="form-group">
         <label for="email">Email</label>
         <input type="email" class="form-control" id="email" name="email" required>
      </div>
   
      <div class="form-group">
         <label for="phone">Phone</label>
         <input type="text" class="form-control" id="phone" name="phone" required>
      </div>
   
      <div class="form-group">
         <label for="password">Password</label>
         <input type="password" class="form-control" id="password" name="password" required>
      </div>
   
      <div class="form-group">
         <label for="password_confirmation">Confirm Password</label>
         <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
      </div>
   
      <div class="form-group">
         <label for="gender">Gender</label>
         <select class="form-control" id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
         </select>
      </div>
   
      <div class="form-group">
         <label for="address">Address</label>
         <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
      </div>
   
      <div>
         <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
              </div><!-- /.card-body -->
            </div>
          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-5 connectedSortable">
  
            <!-- Map card 
            <div class="card bg-gradient-primary">
              <div class="card-header border-0">
                <h3 class="card-title">
        
                  change password
                </h3>
              </div>-->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    Profile
                  </h3>
            </div>

            <div class="form-group">
              <label for="password">Old Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
          </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        </div>
        
            <!-- /.card -->
  


            <!-- solid sales graph -->
          
            
            <div class="card" >

                <div class="card-header">
                 <h3 class="card-title">
                Setup 2 Step Verification
               </h3>
              </div>
                    <div >
                     <img class="direct-chat-img" src="admin/dist/img/user1-128x128.jpg" alt="message user image">
                     Authenicator App
                     </div>
            </div>
              
          </section>
        </div>
      </div>
    </section>
  </div>
      <!-- Main content-->
</div>
@end('section')