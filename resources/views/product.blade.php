
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Products</div>

                <div class="card-body">
                    <div>
                        <button id="btn-add" name="btn-add" class="btn btn-primary btn-xs">Add New Product</button>
                    </div>
                    
                    <div>
                        @if(!empty($products))
                        <table class="table table-inverse">
                            <thead>
                                <tr>
                                    <th>Product name</th>
                                    <th>Quantity in stock</th>
                                    <th>Price per item</th>
                                    <th>Datetime submitted</th>
                                    <th>Total value number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <?php
                            $total_amount = 0;
                            ?>
                            <tbody id="product-list" name="product-list">
                                @foreach ($products as $product)
                                <?php $amount =  $product->quantity * $product->price ?>
                                <tr id="product{{$product->id}}">
                                    <td>{{$product->name}}</td>
                                    <td>{{$product->quantity}}</td>
                                    <td><?php echo '$'.number_format($product->price,2); ?></td>
                                    <td><?php echo date('m-d-Y',$product->date); ?></td>
                                    <td><?php echo '$'.number_format($amount,2); ?></td>
                                    <td>
                                        <button class="btn btn-info open-modal" value="{{$product->id}}">Edit
                                        </button>
                                        <button class="btn btn-danger delete-product" value="{{$product->id}}">Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php 
                                $total_amount = $total_amount+$amount;
                                
                                ?>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right">
                                        <strong>Total: </strong>
                                    </td>
                                    <td colspan="2" style="text-align: left"><strong><?php echo '$'.number_format($total_amount,2); ?></strong></td>
                                </tr>
                                <input type="hidden" id="total_val" value="<?php echo $total_amount; ?>" />
                            </tfoot>
                        </table>
                        @endif
                        <div id="productEditorModal" style="display: none;" aria-hidden="true">
                            <h4 class="modal-title" id="productEditorModalLabel">Add Product</h4>
                            <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">

                                <div class="form-group">
                                    <label for="name" class="col-sm-5 control-label">Product Name</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="name" name="name"
                                               placeholder="Product Name" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="quantity" class="col-sm-5 control-label">Product Quantity</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="quantity" name="quantity"
                                               placeholder="Product Quantity" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="price" class="col-sm-5 control-label">Product Price</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="price" name="price"
                                               placeholder="Product Price" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="col-sm-12 btn btn-primary" id="btn-save" value="add">Save Product</button>
                                    <input type="hidden" name="id" id="id" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('.date_submitted').datepicker({
        format: 'dd-mm-yyyy'
    });
    jQuery(document).ready(function ($) {
        ////----- Open the modal to CREATE a product -----////
        jQuery('#btn-add').click(function () {
            jQuery('#btn-save').val("add");
            jQuery('#modalFormData').trigger("reset");
            jQuery('#productEditorModal').modal();
        });

        ////----- Open the modal to UPDATE a product -----////
        jQuery('body').on('click', '.open-modal', function () {
            var id = $(this).val();
            $.get('product/' + id, function (data) {
                jQuery('#id').val(id);
                jQuery('#name').val(data.name);
                jQuery('#quantity').val(data.quantity);
                jQuery('#price').val(data.price);
                jQuery('#btn-save').val("update");
                jQuery('#productEditorModal').modal();
            })
        });

        // Clicking the save button on the open modal for both CREATE and UPDATE
        $("#btn-save").click(function (e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var formData = {
                name: jQuery('#name').val(),
                quantity: jQuery('#quantity').val(),
                price: jQuery('#price').val(),
                id: jQuery('#price').val(),
            };
            var state = jQuery('#btn-save').val();
            var type = "POST";
            var id = jQuery('#id').val();
            var ajaxurl = 'addProduct';
            if (state == "update") {
                type = "PUT";
                ajaxurl = 'updateProduct/' + id;
            }
            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    var product = '<tr id="product' + data.id + '"><td>' + data.name + '</td><td>' + data.quantity + '</td><td>' + data.price + '</td><td>' + data.date + '</td><td>' + data.total + '</td>';
                    product += '<td><button class="btn btn-info open-modal" value="' + data.id + '">Edit</button>&nbsp;';
                    product += '<button class="btn btn-danger delete-product" value="' + data.id + '">Delete</button></td></tr>';
                    if (state == "add") {
                        jQuery('#product-list').append(product);
                    } else {
                        $("#product" + id).replaceWith(product);
                    }
                    jQuery('#modalFormData').trigger("reset");
                    $.modal.close();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });

        ////----- DELETE a product and remove from the page -----////
        jQuery('.delete-product').click(function () {
            var id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "DELETE",
                url: 'deleteProdct/' + id,
                success: function (data) {
                    console.log(data);
                    $("#product" + id).remove();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });
    });
</script>
<style>
    #productEditorModal{
        overflow: visible !important;
    }
</style>
@endsection