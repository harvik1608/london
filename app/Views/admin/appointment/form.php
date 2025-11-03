<div class="content-header row">
    <?php $this->load->view('admin/common/breadcrumb', true); ?>
</div>
<div class="content-body">
    <section class="basic-inputs">
        <div class="row match-height">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <?php
                                if(!empty($languages))
                                {
                                    foreach($languages as $key => $val)
                                    {
                            ?>
                                        <li class="nav-item">
                                            <a href="#<?php echo $val['language_name'].'_'.$val['lan_id']; ?>" data-toggle="tab" aria-expanded="false" class="nav-link <?php if($key == 0){ echo "active";}?>">
                                                <span class="d-none d-sm-inline-block"><?php echo $val['language_name'];?></span>   
                                            </a>
                                        </li>
                            <?php
                                    }
                                } 
                            ?>
                        </ul><br>
                        <form class="form-horizontal" id="media_form" role="form" method="POST" action="<?= $action ?>" enctype="multipart/form-data">
                            <input type="hidden" name="event_id" value="<?= isset($category['event_id']) ? $category['event_id'] : '' ?>" />
                            <input type="hidden" name="evt_id" value="<?= isset($category['evt_id']) ? $category['evt_id'] : '' ?>" />
                            <input type="hidden" name="image_1" value="<?= isset($category['old_image1']) ? $category['old_image1'] : '' ?>" />
                            <input type="hidden" name="image_2" value="<?= isset($category['old_image2']) ? $category['old_image2'] : '' ?>" />
                            <input type="hidden" name="image_3" value="<?= isset($category['old_image3']) ? $category['old_image3'] : '' ?>" />
                            <!-- Offline bank details fields -->
                            <input type="hidden" name="offline_account_name" id="offline_account_name" value="<?php echo $bank_info['account_name']; ?>" />
                            <input type="hidden" name="offline_name" id="offline_name" value="<?php echo $bank_info['bank_name']; ?>" />
                            <input type="hidden" name="offline_branch" id="offline_branch" value="<?php echo $bank_info['branch']; ?>" />
                            <input type="hidden" name="offline_account_no" id="offline_account_no" value="<?php echo $bank_info['account_no']; ?>" />
                            <input type="hidden" name="offline_ifsc_code" id="offline_ifsc_code" value="<?php echo $bank_info['ifsc_code']; ?>" />
                            <input type="hidden" name="offline_whatsapp_no" id="offline_whatsapp_no" value="<?php echo $bank_info['whatsapp_no']; ?>" />
                            <input type="hidden" name="offline_email" id="offline_email" value="<?php echo $bank_info['email']; ?>" />

                            <div class="row">
                                <div class="col-12">
                                    <div class="card-box">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label pt-0" for="status">Is Meditation Event?</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="d-inline-block custom-control custom-radio mr-1">
                                                        <input type="radio" name="is_meditation_event" class="custom-control-input" id="enable_meditation" value="1" <?= (isset($category['is_meditation_event']) && $category['is_meditation_event'] == '1') ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="enable_meditation">Yes</label>
                                                    </div>
                                                    <div class="d-inline-block custom-control custom-radio">
                                                        <input type="radio" name="is_meditation_event" class="custom-control-input" id="disable_meditation" value="0" <?= (isset($category['is_meditation_event']) && $category['is_meditation_event'] == '0') ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="disable_meditation">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="title">Category</label>
                                            <div class="col-sm-10">
                                                <select id="event_category" name="event_category" class="form-control">
                                                    <option value="">Category</option>
                                                    <option value="0" <?php echo (isset($category['event_category']) && $category['event_category'] == '0') ? "selected" : ""; ?>>Monthly Event</option>
                                                    <option value="1" <?php echo (isset($category['event_category']) && $category['event_category'] == '1') ? "selected" : ""; ?>>Annual Event</option>
                                                    <option value="2" <?php echo (isset($category['event_category']) && $category['event_category'] == '2') ? "selected" : ""; ?>>Special Event</option>
                                                </select>
                                                <small id="error_event_category" class="form-text form-error text-danger"><?php echo isset($validation['title']) ? $validation['title'] : '' ?></small>
                                            </div>
                                        </div>
                                        <div class="tab-content">
                                            <?php
                                                foreach($languages as $key => $val)
                                                {
                                            ?>
                                                    <div class="tab-pane fade show <?php if($key == 0){ echo "active";} ?>" id="<?php echo $val['language_name'].'_'.$val['lan_id']; ?>">
                                                        <div class="form-group row">
                                                            <label class="col-sm-2 col-form-label" for="title">Title</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" id="title" name="title[<?php echo $val['lan_status'];?>]" class="form-control" placeholder="Title" value="<?php if(isset($mediaLang)){ echo $mediaLang[$val['lan_status']]['title'];} ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-2 col-form-label" for="description">Sub Title</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" id="title_1" name="sub_title[<?php echo $val['lan_status'];?>]" class="form-control" placeholder="Sub Title" value="<?php if(isset($mediaLang)){ echo $mediaLang[$val['lan_status']]['sub_title'];} ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-2 col-form-label" for="description">Sub Description</label>
                                                            <div class="col-sm-10">
                                                                <textarea id="desc_1" name="description[<?php echo $val['lan_status'];?>]" class="form-control" placeholder="Sub Description"><?php if(isset($mediaLang)){ echo $mediaLang[$val['lan_status']]['description'];} ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-2 col-form-label" for="location">Overview</label>
                                                            <div class="col-sm-10">
                                                                <textarea id="overview" name="overview[<?php echo $val['lan_status'];?>]" class="form-control" placeholder="Overview"><?php if(isset($mediaLang)){ echo $mediaLang[$val['lan_status']]['overview'];} ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php 
                                                }
                                            ?>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Image</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="image_1" name="image_1" class="form-control" placeholder="Image" accept="image/*">
                                                <small id="error_image_1" class="form-text form-error text-danger"></small>
                                                <?php if(isset($category['image_1'])){ ?>
                                                    <br>
                                                    <img src="<?php echo $category['image_1']; ?>" width="100">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display: none;">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Title 2</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="title_2" name="title_2" class="form-control" placeholder="Sub Title 2" value="<?= isset($category['title_2']) ? $category['title_2'] : '' ?>" />
                                                <small id="error_title_2" class="form-text form-error text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display: none;">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Description 2</label>
                                            <div class="col-sm-10">
                                                <textarea id="desc_2" name="desc_2" class="form-control" placeholder="Sub Description 2"><?= isset($category['desc_2']) ? $category['desc_2'] : '' ?></textarea>
                                                <small id="error_desc_2" class="form-text form-error text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display: none;">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Image 2</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="image_2" name="image_2" class="form-control" placeholder="Image" accept="image/*">
                                                <small id="error_image_2" class="form-text form-error text-danger"></small>
                                                <?php if(isset($category['image_2'])){ ?>
                                                    <br>
                                                    <img src="<?php echo $category['image_2']; ?>" width="100">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display: none;">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Title 3</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="title_3" name="title_3" class="form-control" placeholder="Sub Title 3" value="<?= isset($category['title_3']) ? $category['title_3'] : '' ?>" />
                                                <small id="error_title_3" class="form-text form-error text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display: none;">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Description 3</label>
                                            <div class="col-sm-10">
                                                <textarea id="desc_3" name="desc_3" class="form-control" placeholder="Sub Description 3"><?= isset($category['desc_3']) ? $category['desc_3'] : '' ?></textarea>
                                                <small id="error_desc_3" class="form-text form-error text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="display: none;">
                                            <label class="col-sm-2 col-form-label" for="description">Sub Image 3</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="image_3" name="image_3" class="form-control" placeholder="Image" accept="image/*">
                                                <small id="error_image_3" class="form-text form-error text-danger"></small>
                                                <?php if(isset($category['image_3'])){ ?>
                                                    <br>
                                                    <img src="<?php echo $category['image_3']; ?>" width="100">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="description">Description</label>
                                            <div class="col-sm-10">
                                                <textarea id="description" name="description" class="form-control" placeholder="Description">< ?= isset($category['description']) ? $category['description'] : '' ?></textarea>
                                                <small id="error_description" class="form-text form-error text-danger">< ?php echo isset($validation['description']) ? $validation['description'] : '' ?></small>
                                            </div>
                                        </div> -->
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="image">Thumbnail</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="image" name="image" class="form-control" placeholder="Image" accept="image/*">
                                                <?php if(isset($category['image'])){ ?>
                                                    <br>
                                                    <img src="<?php echo $category['image']; ?>" width="100">
                                                <?php } ?>
                                                <small id="error_image" class="form-text form-error text-danger"><?php echo isset($validation['image']) ? $validation['image'] : '' ?></small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="location">Location</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="location" name="location" class="form-control" placeholder="Location" value="<?= isset($category['location']) ? $category['location'] : '' ?>">
                                                <small id="error_location" class="form-text form-error text-danger"><?php echo isset($validation['location']) ? $validation['location'] : '' ?></small>
                                                <input type="hidden" id="latitude" name="latitude" value="<?= isset($category['latitude']) ? $category['latitude'] : ''; ?>">
                                                <input type="hidden" id="longitude" name="longitude" value="<?= isset($category['longitude']) ? $category['longitude'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="event_date">Event Date</label>
                                            <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type='text' name="event_date" id="event_date" class="form-control" placeholder="Event Date" autocomplete="off" value="<?= isset($category['event_date']) ? $category['event_date'] : '' ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="ion-calendar"></i></span>
                                                </div>
                                            </div>  
                                            <small id="error_event_date" class="form-text form-error text-danger"><?php echo isset($validation['event_date']) ? $validation['event_date'] : '' ?></small>
                                            </div> 
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="event_date">Event End Date</label>
                                            <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type='text' name="event_end_date" id="event_end_date" class="form-control" placeholder="Event End Date" autocomplete="off" value="<?= isset($category['event_end_date']) ? $category['event_end_date'] : '' ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="ion-calendar"></i></span>
                                                </div>
                                            </div>  
                                            <small id="error_event_date" class="form-text form-error text-danger"><?php echo isset($validation['event_date']) ? $validation['event_date'] : '' ?></small>
                                            </div> 
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="event_type">Type</label>
                                            <div class="col-sm-10">
                                                <select id="event_type" name="event_type" class="form-control">
                                                    <option value="0" <?php echo isset($category['event_type']) && $category['event_type'] == 0? 'selected' : ''; ?>>Free</option>
                                                    <option value="1" <?php echo isset($category['event_type']) && $category['event_type'] == 1? 'selected' : ''; ?>>Paid</option>
                                                </select>
                                                <small id="error_event_type" class="form-text form-error text-danger"><?php echo isset($validation['event_type']) ? $validation['event_type'] : '' ?></small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="price">Price</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="price" name="price" class="form-control" placeholder="Price" value="<?= isset($category['price']) ? $category['price'] : '' ?>">
                                                <small id="error_price" class="form-text form-error text-danger"><?php echo isset($validation['price']) ? $validation['price'] : '' ?></small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="image">Images</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="image" name="images[]" multiple="multiple" class="form-control" accept="image/*">
                                                <?php if(isset($category['media'][0])){ ?>
                                                    <br>
                                                    <div class="row">
                                                    <?php foreach($category['media'][0] as $image){ ?>
                                                    <div class="col-md-4">
                                                        <div class="del-img position-relative">
                                                            <img src="<?php echo $image['media']; ?>" style="height: 200px;">
                                                            <i class="la la-close del-btn position-absolute" media_id="<?= $image['id']; ?>"></i>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                <small id="error_image" class="form-text form-error text-danger"><?php echo isset($validation['image']) ? $validation['image'] : '' ?></small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="video">Videos</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="video" name="video[]" multiple="multiple" class="form-control" accept="video/*">
                                                <?php if(isset($category['media'][1])){ ?>
                                                    <br>
                                                    <div class="row">
                                                    <?php foreach($category['media'][1] as $video){ ?>
                                                    <div class="col-md-4">
                                                        <div class="del-img position-relative">
                                                            <video controls>
                                                                <source src="<?php echo $video['media']; ?>">
                                                            </video>
                                                            <i class="la la-close del-btn position-absolute" media_id="<?= $video['id']; ?>"></i>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                <small id="error_video" class="form-text form-error text-danger"><?php echo isset($validation['video']) ? $validation['video'] : '' ?></small>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="location">Total Seats</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="total_seat" name="total_seat" class="form-control" placeholder="Total Seats" value="<?= isset($category['total_seat']) ? $category['total_seat'] : ''; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group row" id="more_benefits">
                                            <?php
                                                if(isset($category['benefits']) && $category['benefits'] != "")
                                                {
                                                    $benefits = json_decode($category['benefits'],true);
                                                    foreach($benefits as $key => $val)
                                                    {
                                            ?>
                                                        <label class="col-sm-2 col-form-label" for="location" id="more_label_element_<?php echo $val['id']; ?>"><?php echo $key+1 == 1 ? "Benefits" : ""; ?></label>
                                                        <div class="col-sm-9" id="more_input_element_<?php echo $val['id']; ?>">
                                                            <input type="text" name="benefits[]" placeholder="Benefits" class="form-control" value="<?php echo urldecode($val['title']); ?>" />
                                                        </div>
                                                        <?php
                                                            if($key+1 == 1)
                                                            {
                                                        ?>
                                                                <div class="col-sm-1">
                                                                    <a href="javascript:;" onclick="load_more('more_benefits','benefits','Benefits');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            } else {
                                                        ?>
                                                                <div class="col-sm-1" id="more_action_element_<?php echo $val['id']; ?>">
                                                                    <a href="javascript:;" onclick="remove_element('more_benefits','<?php echo $val['id']; ?>','benefits',1,'<?php echo $category['event_id']; ?>');"><i class="fa fa-times" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            }
                                                        ?>
                                            <?php
                                                    }
                                                } else {
                                            ?>
                                                    <label class="col-sm-2 col-form-label" for="location">Benefits</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="benefits[]" placeholder="Benefits" class="form-control" />
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <a href="javascript:;" onclick="load_more('more_benefits','benefits','Benefits');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                    </div>
                                            <?php
                                                }
                                            ?>       
                                        </div>
                                        <div class="form-group row" id="more_highlights">
                                            <?php
                                                if(isset($category['highlights']) && $category['highlights'] != "")
                                                {
                                                    $highlights = json_decode($category['highlights'],true);
                                                    foreach($highlights as $key => $val)
                                                    {
                                            ?>
                                                        <label class="col-sm-2 col-form-label" for="location" id="more_label_element_<?php echo $val['id']; ?>"><?php echo $key+1 == 1 ? "Highlights" : ""; ?></label>
                                                        <div class="col-sm-9" id="more_input_element_<?php echo $val['id']; ?>">
                                                            <input type="text" name="highlights[]" placeholder="Highlights" class="form-control" value="<?php echo urldecode($val['title']); ?>" />
                                                        </div>
                                                        <?php
                                                            if($key+1 == 1)
                                                            {
                                                        ?>
                                                                <div class="col-sm-1">
                                                                    <a href="javascript:;" onclick="load_more('more_highlights','highlights','Highlights');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            } else {
                                                        ?>
                                                                <div class="col-sm-1" id="more_action_element_<?php echo $val['id']; ?>">
                                                                    <a href="javascript:;" onclick="remove_element('more_highlights','<?php echo $val['id']; ?>','highlights',1,'<?php echo $category['event_id']; ?>');"><i class="fa fa-times" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            }
                                                        ?>
                                            <?php
                                                    }
                                                } else {
                                            ?>
                                                    <label class="col-sm-2 col-form-label" for="location">Highlights</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="highlights[]" placeholder="Highlights" class="form-control" />
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <a href="javascript:;" onclick="load_more('more_highlights','highlights','Highlights');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                    </div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                        <div class="form-group row" id="more_program_schedule">
                                            <?php
                                                if(isset($category['program_schedule']) && $category['program_schedule'] != "")
                                                {
                                                    $program_schedules = json_decode($category['program_schedule'],true);
                                                    foreach($program_schedules as $key => $val)
                                                    {
                                            ?>
                                                        <label class="col-sm-2 col-form-label" for="location" id="more_label_element_<?php echo $val['id']; ?>"><?php echo $key+1 == 1 ? "Program Schedule" : ""; ?></label>
                                                        <div class="col-sm-9" id="more_input_element_<?php echo $val['id']; ?>">
                                                            <input type="text" name="program_schedule[]" placeholder="Program Schedule" class="form-control" value="<?php echo urldecode($val['title']); ?>" />
                                                        </div>
                                                        <?php
                                                            if($key+1 == 1)
                                                            {
                                                        ?>
                                                                <div class="col-sm-1">
                                                                    <a href="javascript:;" onclick="load_more('more_program_schedule','program_schedule','Program Schedule');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            } else {
                                                        ?>
                                                                <div class="col-sm-1" id="more_action_element_<?php echo $val['id']; ?>">
                                                                    <a href="javascript:;" onclick="remove_element('more_program_schedule','<?php echo $val['id']; ?>','program_schedule',1,'<?php echo $category['event_id']; ?>');"><i class="fa fa-times" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            }
                                                        ?>
                                            <?php
                                                    }
                                                } else {
                                            ?>
                                                    <label class="col-sm-2 col-form-label" for="location">Program Schedule</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="program_schedule[]" placeholder="Program Schedule" class="form-control" />
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <a href="javascript:;" onclick="load_more('more_program_schedule','program_schedule','Program Schedule');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                    </div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                        <div class="form-group row" id="more_facility">
                                            <?php
                                                if(isset($category['facility']) && $category['facility'] != "")
                                                {
                                                    $facilitys = json_decode($category['facility'],true);
                                                    foreach($facilitys as $key => $val)
                                                    {
                                            ?>
                                                        <label class="col-sm-2 col-form-label" for="location" id="more_label_element_<?php echo $val['id']; ?>"><?php echo $key+1 == 1 ? "Facility" : ""; ?></label>
                                                        <div class="col-sm-9" id="more_input_element_<?php echo $val['id']; ?>">
                                                            <input type="text" name="facility[]" placeholder="Facility" class="form-control" value="<?php echo urldecode($val['title']); ?>" />
                                                        </div>
                                                        <?php
                                                            if($key+1 == 1)
                                                            {
                                                        ?>
                                                                <div class="col-sm-1">
                                                                    <a href="javascript:;" onclick="load_more('more_facility','facility','Facility');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            } else {
                                                        ?>
                                                                <div class="col-sm-1" id="more_action_element_<?php echo $val['id']; ?>">
                                                                    <a href="javascript:;" onclick="remove_element('more_facility','<?php echo $val['id']; ?>','facility',1,'<?php echo $category['event_id']; ?>');"><i class="fa fa-times" style="margin-top: 13px !important;"></i></a>
                                                                </div>
                                                        <?php
                                                            }
                                                        ?>
                                            <?php
                                                    }
                                                } else {
                                            ?>
                                                    <label class="col-sm-2 col-form-label" for="location">Facility</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="facility[]" placeholder="Facility" class="form-control" />
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <a href="javascript:;" onclick="load_more('more_facility','facility','Facility');"><i class="fa fa-plus" style="margin-top: 13px !important;"></i></a>
                                                    </div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label pt-0" for="status">Payment Accept Via</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="d-inline-block custom-control custom-radio mr-1">
                                                        <input type="radio" name="payment_mode" class="custom-control-input" id="payment_via1" value="1" <?= (isset($category['payment_mode']) && $category['payment_mode'] == 1) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="payment_via1">Online</label>
                                                    </div>
                                                    <div class="d-inline-block custom-control custom-radio mr-1">
                                                        <input type="radio" name="payment_mode" class="custom-control-input" id="payment_via2" value="2" <?= (isset($category['payment_mode']) && $category['payment_mode'] == 2) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="payment_via2">Offline</label>
                                                    </div>
                                                    <div class="d-inline-block custom-control custom-radio mr-1">
                                                        <input type="radio" name="payment_mode" class="custom-control-input" id="payment_via0" value="0" <?= (isset($category['payment_mode']) && $category['payment_mode'] == 0) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="payment_via0">Both</label>
                                                    </div>
                                                    <?php
                                                        if((isset($category['payment_mode']) && $category['payment_mode'] == 2) || (isset($category['payment_mode']) && $category['payment_mode'] == 0))
                                                            echo '<a href="javascript:;" id="offline_bank_detail"><small>Edit Bank Detail</small></a>';
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label pt-0" for="status">Status</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="d-inline-block custom-control custom-radio mr-1">
                                                        <input type="radio" name="status" class="custom-control-input" id="enable" value="1" <?= (isset($category['status']) && $category['status'] == 1) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="enable">Enable</label>
                                                    </div>
                                                    <div class="d-inline-block custom-control custom-radio">
                                                        <input type="radio" name="status" class="custom-control-input" id="disable" value="0" <?= (isset($category['status']) && $category['status'] == 0) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="disable">Disable</label>
                                                    </div>
                                                </div>
                                                <small id="error_status" class="form-text form-error text-danger"><?php echo isset($validation['status']) ? $validation['status'] : '' ?></small>
                                            </div>
                                        </div>
                                    </div> <!-- end card-box -->
                                    
                                    <div class="form-group text-right mb-0">
                                        <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">Submit</button>
                                    </div>
                                </div><!-- end col -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal" tabindex="-1" role="dialog" id="eventBankDetailModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Offline Payment Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Account Name</label>
                    <div class="col-sm-10">
                        <input type="text" id="bank_account_name" name="bank_account_name" class="form-control" placeholder="Account Name" value="<?php echo $bank_info['account_name']; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Bank Name</label>
                    <div class="col-sm-10">
                        <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="Bank Name" value="<?php echo $bank_info['bank_name']; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Branch Name</label>
                    <div class="col-sm-10">
                        <input type="text" id="bank_branch" name="bank_branch" class="form-control" placeholder="Branch Name" value="<?php echo $bank_info['branch']; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Account No.</label>
                    <div class="col-sm-10">
                        <input type="text" id="account_no" name="account_no" class="form-control" placeholder="Account no." value="<?php echo $bank_info['account_no']; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">IFSC Code</label>
                    <div class="col-sm-10">
                        <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" placeholder="IFSC code" value="<?php echo $bank_info['ifsc_code']; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Whatsapp No.</label>
                    <div class="col-sm-10">
                        <input type="text" id="whatsapp_no" name="whatsapp_no" class="form-control" placeholder="Whatsapp no." value="<?php echo $bank_info['whatsapp_no']; ?>" />
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Email</label>
                    <div class="col-sm-10">
                        <input type="text" id="bank_email" name="bank_email" class="form-control" placeholder="Email" value="<?php echo $bank_info['email']; ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_bank_detail()">Save</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIoBSWkLnORdWpL70Cz8qWyPwtI2W_IBc&callback=initMap&libraries=places"></script>
<script type="text/javascript">
    function initialize() 
    {
        var input = document.getElementById('location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        ajaxValidation('#media_form', "<?= $validation_url ?>");
        $(".del-btn").click(function(){
            var media_id = $(this).attr('media_id');
            console.log(media_id);
            $.ajax({
              url: "<?php echo base_url().'admin/event/delmedia'; ?>",
              context: this,
              type: "POST",
              data : { media_id : media_id},
              success: function(result){
                $(this).parents('.col-md-4').remove();
                jQuery.NotificationApp.send("Well Done!", "Deleted successfully.", "bottom-right", "#5ba035", "success")
              }
            });
        });
    });

    $(document).ready(function() {
        $('#event_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
        $('#event_end_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    
        $('#description').summernote({
            height: 300,
            codemirror:{ 
                theme: 'monokai'
            }
        });
        $("#offline_bank_detail").click(function(){
            $('#eventBankDetailModal').modal({backdrop: 'static', keyboard: false});
        });

        $("#payment_via2,#payment_via0").click(function(){
            if($(this).prop("checked") == true)
            {
                $("#offline_bank_detail").show();
                $('#eventBankDetailModal').modal({backdrop: 'static', keyboard: false});
            }
        });
        $("#payment_via1").click(function(){
            if($(this).prop("checked") == true)
                $("#offline_bank_detail").hide();
            else 
                $("#offline_bank_detail").show();
        });
    });
    function save_bank_detail()
    {
        $("#offline_account_name").val($("#bank_account_name").val());
        $("#offline_name").val($("#bank_name").val());
        $("#offline_branch").val($("#bank_branch").val());
        $("#offline_account_no").val($("#account_no").val());
        $("#offline_ifsc_code").val($("#ifsc_code").val());
        $("#offline_whatsapp_no").val($("#whatsapp_no").val());
        $("#offline_email").val($("#bank_email").val());
        $('#eventBankDetailModal').modal('hide');
    }

function load_more(element,name,title)
{
    var content = "";
    var no = $("#"+element+" label[id^=more_label_element_]").length+1;

    content += '<label class="col-sm-2 col-form-label" for="location" id="more_label_element_'+no+'"></label>';
    content += '<div class="col-sm-9" id="more_input_element_'+no+'">';
    content += '<input type="text" name="'+name+'[]" placeholder="'+title+'" class="form-control" />';
    content += '</div>';
    content += '<div class="col-sm-1" id="more_action_element_'+no+'">';
    content += '<a href="javascript:;" onclick=remove_element("'+element+'","'+no+'","'+name+'",0);><i class="fa fa-times" style="margin-top: 13px !important;"></i></a>';
    content += '</div>';
    $("#"+element).append(content);
}
function remove_element(element,no,name,flag,event_id = 0)
{
    if(flag == 0)
    {
        $("#"+element+" #more_label_element_"+no).remove();
        $("#"+element+" #more_input_element_"+no).remove();
        $("#"+element+" #more_action_element_"+no).remove();
    } else {
        if(confirm("Are you sure to remove this?"))
        {
            $.ajax({
                url: "<?php echo base_url().'admin/event/remove_event_element'; ?>",
                context: this,
                type: "POST",
                data : { 
                    no:no,
                    name: name,
                    event_id: event_id
                },
                dataType: 'json',
                success: function(result){
                    if(result.status == 1)
                    {
                        $("#"+element+" #more_label_element_"+no).remove();
                        $("#"+element+" #more_input_element_"+no).remove();
                        $("#"+element+" #more_action_element_"+no).remove();
                    }
                }
            });
        }
    }
}
</script>