<?php
    function GetRegistrationEmail(string $token) {
        return ('
                <html xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://assets.sportifyapp.co/css/email/main.css">

</head>

<body>

<center>
    <div align="center">
        <table border="0" cellspacing="0" cellpadding="0" width="100%" class="aw-bgc" align="center"
               role="presentation"
               style="background-color: rgb(248, 248, 248); font-weight: 400; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px; height: 100%;">
            <tbody>
            <tr>
                <td class="temp-wrapper"
                    style="text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                    <div align="center">

                        <!--[if (gte mso 9)]><table border="0" cellspacing="0" cellpadding="0" width="600" align="center" role="presentation"><tr><td class="temp-header"><![endif]-->
                        <div class="temp-header" style="max-width: 600px; ">
                            <div class="temp-fullbleed contained" style="max-width: 600px; width: 100%;">
                                <div class="region">
                                    <div>
                                        <table class="row aw-stack"
                                               style="width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;"
                                               role="presentation">
                                            <tbody>
                                            <tr>
                                                <td class="container" style="padding: 30px 20px; width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse;
border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;" width="100%" valign="top">
                                                    <div class="definition-parent"><span>
                                                                        <table align="center" width="100%"
                                                                               class="floated-none"
                                                                               style="float: none; text-align: center; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;"
                                                                               role="presentation">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="center"
                                                                                        style="padding: 0px; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">

                                                                                        <img class="model"
                                                                                             src="https://assets.sportifyapp.co/img/inline.png"
                                                                                             style="display: block; width: 100%; height: 41px; border-width: 0px; border-style: none; line-height: 100%; max-width: 100%; outline-width: medium; outline-style: none; text-decoration: none; color: rgb(51, 51, 51); font-size: 20px; font-weight: 700; border-radius: 0px;"
                                                                                             alt="Logo" width="150"
                                                                                             height="41">

                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </span></div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--[if (gte mso 9)]></td></tr></table><![endif]-->

                        <!--[if (gte mso 9)]><table border="0" cellspacing="0" cellpadding="0" width="600" align="center" bgcolor="#ffffff" role="presentation"><tr><td class="temp-body"><![endif]-->
                        <div class="temp-body"
                             style="background-color:#ffffff; border-radius:10px; max-width: 600px; ">
                            <div class="temp-fullbleed contained" style="max-width: 600px; width: 100%;">
                                <div class="region">
                                    <div>
                                        <table class="row aw-stack"
                                               style="width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;"
                                               role="presentation">
                                            <tbody>
                                            <tr>
                                                <td class="container" style="padding: 30px; width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse:
collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;" width="100%" valign="top">
                                                    <div class="definition-parent">
                                                        <div class="text-element paragraph">
                                                            <div
                                                                    style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                <h4>Creare la tua associazione </h4>

                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    &nbsp;</p>

                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                            You received this email because your new athlete association was created. In order to activate it click the button below, it will allow you to create the main association account where you then have all rights</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="definition-parent">
                                                        <div class="divider">
                                                            <table cellpadding="0" cellspacing="0"
                                                                   width="100%" role="presentation"
                                                                   style="text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                                                                <tbody>
                                                                <tr>
                                                                    <td class="divider-container"
                                                                        style="padding: 20px 0px; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                                                                        <table width="100%"
                                                                               role="presentation"
                                                                               style="border-width: 1px 0px 0px; border-style: solid none none; border-top-color: rgb(222, 224, 232); border-collapse: collapse; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-spacing: 0px; font-size: 18px;">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td width="100%"
                                                                                    style="text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                                                                                    <!--<hr style="border-top-style:none; border-left-style:none; border-right-style:none;"/>-->
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="definition-parent">
                                                        <div class="text-element paragraph">
                                                            <div
                                                                    style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    <em
                                                                            data-stringify-type="italic"><a
                                                                            class="validating"
                                                                            href="https://login.sportifyapp.co/register.php?token=' . $token . '"
                                                                            style="color: rgb(0, 0, 0); font-weight: bold;"
                                                                            target="_blank"
                                                                            rel="noopener noreferrer">
                                                                                Click here to create president account</a></p>

                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    <br>
Good Luck &amp; have fun!</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</center>

</body>

</html>'
        );
    }
    function GetWelcomeEmail($customer_name) {
        return '
        
<html xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>

<center>
    <div align="center">
        <table border="0" cellspacing="0" cellpadding="0" width="100%" class="aw-bgc" align="center"
               role="presentation"
               style="background-color: rgb(248, 248, 248); font-weight: 400; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px; height: 100%;">
            <tbody>
            <tr>
                <td class="temp-wrapper"
                    style="text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                    <div align="center">

                        <!--[if (gte mso 9)]><table border="0" cellspacing="0" cellpadding="0" width="600" align="center" role="presentation"><tr><td class="temp-header"><![endif]-->
                        <div class="temp-header" style="max-width: 600px; ">
                            <div class="temp-fullbleed contained" style="max-width: 600px; width: 100%;">
                                <div class="region">
                                    <div>
                                        <table class="row aw-stack"
                                               style="width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;"
                                               role="presentation">
                                            <tbody>
                                            <tr>
                                                <td class="container" style="padding: 30px 20px; width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse;
border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;" width="100%" valign="top">
                                                    <div class="definition-parent"><span>
                                                                        <table align="center" width="100%"
                                                                               class="floated-none"
                                                                               style="float: none; text-align: center; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;"
                                                                               role="presentation">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="center"
                                                                                        style="padding: 0px; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">

                                                                                        <img class="model"
                                                                                             src="https://assets.sportifyapp.co/img/inline.png"
                                                                                             style="display: block; width: 100%; height: auto; border-width: 0px; border-style: none; line-height: 100%; max-width: 100%; outline-width: medium; outline-style: none; text-decoration: none; color: rgb(51, 51, 51); font-size: 20px; font-weight: 700; border-radius: 0px;"
                                                                                             alt="Logo" width="150"
                                                                                             height="41">

                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </span></div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--[if (gte mso 9)]></td></tr></table><![endif]-->

                        <!--[if (gte mso 9)]><table border="0" cellspacing="0" cellpadding="0" width="600" align="center" bgcolor="#ffffff" role="presentation"><tr><td class="temp-body"><![endif]-->
                        <div class="temp-body"
                             style="background-color:#ffffff; border-radius:10px; max-width: 600px; ">
                            <div class="temp-fullbleed contained" style="max-width: 600px; width: 100%;">
                                <div class="region">
                                    <div>
                                        <table class="row aw-stack"
                                               style="width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;"
                                               role="presentation">
                                            <tbody>
                                            <tr>
                                                <td class="container" style="padding: 30px; width: 100%; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse:
collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;" width="100%" valign="top">
                                                    <div class="definition-parent">
                                                        <div class="text-element paragraph">
                                                            <div
                                                                    style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                <h4>You have been invited to ' . $customer_name . '</h4>

                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    &nbsp;</p>

                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    Hi, your association has just been created run to configure <it></it></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="definition-parent">
                                                        <div class="divider">
                                                            <table cellpadding="0" cellspacing="0"
                                                                   width="100%" role="presentation"
                                                                   style="text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                                                                <tbody>
                                                                <tr>
                                                                    <td class="divider-container"
                                                                        style="padding: 20px 0px; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                                                                        <table width="100%"
                                                                               role="presentation"
                                                                               style="border-width: 1px 0px 0px; border-style: solid none none; border-top-color: rgb(222, 224, 232); border-collapse: collapse; text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-spacing: 0px; font-size: 18px;">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td width="100%"
                                                                                    style="text-size-adjust: 100%; color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; border-collapse: collapse; border-spacing: 0px; border-width: 0px; border-style: none; font-size: 18px;">
                                                                                    <!--<hr style="border-top-style:none; border-left-style:none; border-right-style:none;"/>-->
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="definition-parent">
                                                        <div class="text-element paragraph">
                                                            <div
                                                                    style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    <em
                                                                            data-stringify-type="italic"><a
                                                                                class="validating"
                                                                                href="https://login.sportifyapp.co/?goto=dashboard"
                                                                                style="color: rgb(0, 0, 0); font-weight: bold;"
                                                                                target="_blank"
                                                                                rel="noopener noreferrer">
                                                                            Click here to login</a></p>

                                                                <p
                                                                        style="color: rgb(51, 51, 51); font-family: Helvetica, Arial, sans-serif; font-size: 18px; line-height: 125%; font-weight: 400; text-align: left;">
                                                                    <br>
                                                                    Good Luck &amp; have fun!</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</center>

</body>

</html>
        ';
    }
?>
