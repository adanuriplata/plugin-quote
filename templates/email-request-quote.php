<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i&display=swap" rel="stylesheet">
  <title>Request a Quote</title>
  <style type="text/css">
    a#track-id-pestmonster:hover {
      text-decoration: underline !important;
    }
  </style>
</head>
<body>

  <table border="print_r($filled_form_fields);0" cellpadding="0" cellspacing="0" width="100%" align="center" bgcolor="#ffffff" style="box-sizing: border-box;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%;min-width: 100%;">
    <tbody>
      <tr>
          <td>
              <div style="border-radius:4px; box-sizing: border-box;width: 100%;vertical-align: top;max-width: 675px;margin: 50px auto;text-align: center;line-height: inherit;min-width: 0 !important;border:1px solid #bebebe;">

                    <!-- Top Header Start -->
                    <div style="box-sizing: border-box;width: 100%;vertical-align: top;max-width: 675px;margin: 0 auto;text-align: center;line-height: inherit;min-width: 0 !important; background-color: #8a1a24;">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="">
                            <tr>
                                <td style="text-align: center; padding: 10px 0px 0px 0px;">
                                    <h3 style=" color:white; font-size: 50px;margin: 30px;font-family: 'Lato', sans-serif;">
                                        Request a quote
                                    </h3>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- header banner start -->
                    <div style="box-sizing: border-box;width: 100%;vertical-align: top;max-width: 675px;margin: 0 auto;text-align: center;line-height: inherit;min-width: 0 !important; background-repeat: no-repeat;background-size: cover; color:black; background-position-y: -12px;">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="">
                            <tr>
                                <td style="text-align: center;">
                                    <h3 style="font-size: 18px;padding-top: 20px;font-family: 'Lato', sans-serif;">
                                        <!-- You have received a request for a quote. The request is the following: -->
                                        Recibiste una nueva cotización
                                    </h3>
                                    <h2 style="font-weight:900;color: #8a1a24;font-size: 25px;padding-top: 10px;font-family: 'Lato', sans-serif;">
                                        Request a Quote
                                    </h2>
                                    <table style="width:100%;border:1px solid #eee;border-collapse:collapse; font-family: 'Lato', sans-serif;">
                                        <thead>
                                            <tr>
                                                <th style="padding:12px;text-align:center;border:1px solid #eee">Producto</th>
                                                <th style="padding:12px;text-align:center;border:1px solid #eee">Cantidad</th>
                                                <th style="padding:12px;text-align:center;border:1px solid #eee">Subtotal</th>
                                            </tr>

                                            <?php foreach ($filled_form_fields['raq_content'] as $key => $value) {

                                                $product = wc_get_product( $value['product_id'] );?>
                                                <tr>
                                                    <td style="padding:12px;text-align:center;border:1px solid #eee">
                                                        <a target="_blank" href="<?php echo get_permalink($value['product_id']); ?>"><?php echo get_the_title($value['product_id']); ?></a>
                                                    </td>
                                                    <td style="padding:12px;text-align:center;border:1px solid #eee">
                                                        <?php echo $value['quantity']; ?>
                                                    </td>
                                                    <td style="padding:12px;text-align:center;border:1px solid #eee">
                                                        <?php echo wc_price($value['quantity'] * $product->get_price());?>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                        </thead>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- header banner end -->

                    <!-- heading section one -->
                    <div style="box-sizing: border-box;width: 100%;vertical-align: top;max-width: 675px;margin: 0 auto;text-align: center;line-height: inherit;min-width: 0 !important;">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="">
                            <tr>
                                <td style="text-align: center; padding: 20px 0px 0px 0px;">
                                    <p style="padding: 0px 100px;font-family: 'Lato', sans-serif;font-size: 18px;color: #b6898f;">
                                        Datos del solicitante:
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- heading section one end -->

                    <!-- heading section two -->
                    <div style="padding-bottom:20px;box-sizing: border-box;width: 100%;vertical-align: top;max-width: 675px;margin: 0 auto;text-align: center;line-height: inherit;min-width: 0 !important;">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="">
                            <tr>
                                <td style="text-align: center; padding: 25px 0px 0px 0px;">
                                    <h2 style="text-align: left;padding-left: 65px;font-family: 'Lato', sans-serif;font-size: 18px;color: #8a1a24;margin: 3px 0px;">Nombre:</h2>
                                    <p style="padding: 15px 46px;font-family: 'Lato', sans-serif;font-size: 16px;color: #000; background: #f9eae7; margin: 0px 65px; border-radius: 10px;">
                                        <?php echo $filled_form_fields['user_name']; ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding: 25px 0px 0px 0px;">
                                    <h2 style="text-align: left;padding-left: 65px;font-family: 'Lato', sans-serif;font-size: 18px;color: #8a1a24;margin: 3px 0px;">Correo:</h2>
                                    <p style="padding: 15px 46px;font-family: 'Lato', sans-serif;font-size: 16px;color: #000; background: #f9eae7; margin: 0px 65px; border-radius: 10px;">
                                        <?php echo $filled_form_fields['user_email']; ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding: 25px 0px 0px 0px;">
                                    <h2 style="text-align: left;padding-left: 65px;font-family: 'Lato', sans-serif;font-size: 18px;color: #8a1a24;margin: 3px 0px;">Mensaje:</h2>
                                    <p style="padding: 15px 46px;font-family: 'Lato', sans-serif;font-size: 16px;color: #000; background: #f9eae7; margin: 0px 65px; border-radius: 10px;">
                                        <?php echo $filled_form_fields['user_message']; ?>
                                    </p>
                                    <button style="font-size: 16px; color: white; background: #8a1a24; border: #8a1a24; border-radius: 4px; margin: 30px 0px 30px 0px; padding: 10px 20px; cursor: pointer;">Ver Cotización</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- heading section two end -->

              </div>

          </td>
      </tr>
    </tbody>
  </table>

</body>
</html>