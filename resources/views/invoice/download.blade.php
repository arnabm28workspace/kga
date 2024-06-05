
<!DOCTYPE html>
<html>
<head>
	<title>KGA | {{$invoice->invoice_no}}</title>
</head>
<body onload="downloadInvoice()">
	<table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0" id="invoice_table">
		<tr>
			<td>
				<table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
					<tr>
						<td rowspan="2">
							<p style="margin: 0">Sender</p>
							<p style="margin: 0;"><strong>KGA</strong><br/>
							
							
						</td>
						<td>
							  
							<p style="margin: 0;">Invoice No:<br/>
								<strong>{{$invoice->invoice_no}}</strong>
							</p>
						</td>
						<td>
							<p style="margin: 0;">Invoice Date:<br/><strong>{{ date('d-M-Y', strtotime($invoice->created_at)) }}</strong></p>
						</td>
					</tr>
					<tr>
						<td align="center">
							<p style="margin: 0;">Order No: {{$invoice->sales_order->order_no}}</p>
							<p style="margin: 0;">Order Date: {{date('d-M-Y', strtotime($invoice->sales_order->created_at))}}</p>
						</td>
						
					</tr>
					<tr>
                        @php
                            $customer_details = json_decode($invoice->customer_details);
                        @endphp
						<td>
							<p style="margin: 0;">Bill To: 
								<br/>
								<strong>{{$customer_details->name}}</strong>
								<br/>
								<p style="margin: 0;">Phone : {{$customer_details->phone}}</p>	
								<br/>
								<p style="margin: 0;">
                                    Address:
                                    {{$customer_details->address}}
                                </p>
                            </p>							
						</td>
						<td>
							<p style="margin: 0;">Ship To: 
								<br/>
								<strong>{{$customer_details->name}}</strong>
								<br/>
								<p style="margin: 0;">Phone : {{$customer_details->phone}}</p>
								<br/>
								<p style="margin: 0;">
                                    Address:
                                    {{$customer_details->address}}
                                </p>
                            </p>							
						</td>
						
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table border="1" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
					<tr>
						<th align="center">Sl No.</th>
						<th align="center">Descriptions of Goods</th>
						<th align="center">Total Pcs</th>
						<th align="center">Price per Piece (Exc.Tax)</th>
                        <th align="center">Total Amount (Exc.Tax)</th>
						<th align="center">HSN Code</th>
						<th align="center">GST</th> 
                        <th align="center">Total Amount (Inc.Tax)</th>
					</tr>
                    @php
                        $item_details = json_decode($invoice->item_details);
						$total_invoice_price = 0;
						$total_pcs = 0;
                    @endphp
                    @forelse ($item_details as $key => $items)
                    @php      
                        $exc_tax_pro_price = ($items->price - $items->tax);
                        $count_exc_tax_pro_price = ($items->quantity * $exc_tax_pro_price);  
                        
                        $getGSTAmount = getGSTAmount($items->price,$items->tax);
                        $gst_amount = $getGSTAmount['gst_amount'];
                        $net_price = $getGSTAmount['net_price'];
                        $count_price = ($items->quantity * $net_price);
                        // echo $net_price;
						$total_invoice_price += $items->total_price;
						$total_pcs += $items->quantity;
                    @endphp
                    <tr>
						<td align="center">{{$key}}</td>
						<td align="center">
                            <strong>{{ $items->product_title }}</strong>
                        </td>
						<td align="center">{{ $items->quantity }}</td>		
                        <td align="center">Rs. {{ $items->price_exc_tax }}</td>
                        <td align="center">Rs. {{ $items->total_price_exc_tax }}</td>
						<td align="center">{{ $items->hsn_code }}</td>						
						<td align="center">{{ $items->tax }} %</td>
						<td align="center">Rs. {{ number_format((float)$items->total_price, 2, '.', '') }}</td>
					</tr>
                    
                    @empty
                        
                    @endforelse
                    
                    
                                        					
					<tr>
						<td align="center">
							<strong>Total Items: {{count((array)$item_details)}}</strong>
						</td>
						<td></td>
						<td align="center">
							<strong>{{ $total_pcs }}</strong>
						</td>
						<td></td>
						<td colspan="2"></td>
						<td align="right"><strong>Total Invoice Amount (Inc.Tax):- </strong> </td>
						<td align="center">
							<strong>
								Rs. {{ number_format((float)$invoice->total_amount, 2, '.', '') }}
							</strong>
						</td>						
					</tr>	
										
								
				</table>
			</td>	
				
		</tr>
		<tr>
			<td>
				<table border="0" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
					<tr>
						<td valign="top">
							<p style="margin: 0;">Amount Chargeable (in words)<br/><strong>INR {{getAmountAlphabetically($invoice->total_amount)}}</strong></p>
						</td>
						<td align="right" valign="top"><h4 style="margin: 0;">E. & O.E</h4></td>
					</tr>
					<tr>
						<td style="width: 50%;">
							<p style="margin: 0;"><u>Declaration</u></p>
							<p style="margin: 0;">1. All claims, if any, for shortages or damages must be reported to customer service on the day of delivery through the contact us page on the web store 2. All Disputes are subject to Maharashtra (27) jurisdiction only.</p>
						</td>
						<td align="center" style="width: 50%; border-top: 1px solid #000; border-left: 1px solid #000;">
							<h3>KGA International</h3>
							<h3>Authorised Signatory</h3>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" style="width: 100%; border-collapse: collapse;" cellpadding="10" cellspacing="0">
					<tr>
						<td style="width: 49%;"><p style="margin: 0;"><strong>Bill By</strong></td>
						<td style="width: 2%;" align="center">:</td>
						<td style="width: 49%;"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
    <script>
        function downloadInvoice()
        {
            var print_header = '';
            var divElements = document.getElementById("invoice_table").innerHTML;
            var print_footer = '';

            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;
            //Reset the page's HTML with div's HTML only
            document.body.innerHTML =
                    "<html><head><title></title></head><body><font size='2'>" +
                    divElements + "</font>" + print_footer + "</body>";
            //Print Page
            window.print();
            //Restore orignal HTML
            document.body.innerHTML = oldPage;
            window.close();
            
        }
        
    </script>
</body>
</html>