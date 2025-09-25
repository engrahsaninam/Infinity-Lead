import {toast} from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import {Dialog} from 'quasar'
import router from "./route.js";
//import "quasar/dist/quasar.css"

//import moment from 'moment';
//import feather from "feather-icons";

export const ROUTE_LOGIN = '/login'
export const ROUTE_REGISTER = '/user-register'
export const ROUTE_VERIFY_EMAIL = '/verify-email/:token'
export const ROUTE_FORGOT_PASSWORD = '/forgot-password'
export const ROUTE_PASSWORD_RESET = '/password/reset/:token'
export const ROUTE_GOOGLE_AUTH = '/google-auth/:token'
export const ROUTE_GOOGLE_ALREADY_REGISTERED = '/google-account-already-registered'
export const ROUTE_GOOGLE_LOGIN_ACCOUNT_NOT_FOUND = '/google-login-account-not-found'
export const ROUTE_UNAUTHORIZED = '/unauthorized'

/*-----------------------------------------------*/
export const ROLE_ADMIN = 'admin'
export const ROLE_USER = 'user'

export const ADMIN_EXT='/admin/';
export const ROUTE_ADMIN_HOME = ADMIN_EXT+'dashboard'
export const ROUTE_ADMIN_WEBSITES= `${ADMIN_EXT}websites`;
export const ROUTE_ADMIN_SENDING_SERVER= `${ADMIN_EXT}sending-servers`;
export const ROUTE_ADMIN_SENDING_SERVER_UPDATE= `${ADMIN_EXT}sending-servers/update/:id`;
export const ROUTE_ADMIN_ADMINS= `${ADMIN_EXT}admins`;
export const ROUTE_ADMIN_ADMINS_CREATE= `${ADMIN_EXT}admins/create/:id?`;

export const ROUTE_ADMIN_CUSTOMERS= `${ADMIN_EXT}customers`;
export const ROUTE_ADMIN_CUSTOMERS_CREATE= `${ADMIN_EXT}customers/create/:id?`;
export const ROUTE_ADMIN_SUBSCRIPTIONS= `${ADMIN_EXT}subscriptions`;
export const ROUTE_ADMIN_TESTIMONIALS= `${ADMIN_EXT}testimonials`;
export const ROUTE_ADMIN_FAQ= `${ADMIN_EXT}faq`;
export const ROUTE_ADMIN_CURRENCY= `${ADMIN_EXT}currencies`;
export const ROUTE_ADMIN_PLANS= `${ADMIN_EXT}plans`;
export const ROUTE_ADMIN_PAYMENT_METHODS= `${ADMIN_EXT}payment-methods`;
export const ROUTE_ADMIN_TEAM= `${ADMIN_EXT}team`;
export const ROUTE_ADMIN_TERMS_CONDITIONS= `${ADMIN_EXT}terms-and-conditions`;
export const ROUTE_ADMIN_PRIVACY_POLICY= `${ADMIN_EXT}privacy-policy`;
export const ROUTE_ADMIN_NAME_VALIDATION= `${ADMIN_EXT}name-validation`;

export const USER_EXT='/user/';
export const ROUTE_USER_HOME = USER_EXT+'dashboard'
export const ROUTE_USER_LEAD_FINDER = USER_EXT+'lead-finder'
export const ROUTE_USER_SALES_CRM = USER_EXT+'sales-crm'
export const ROUTE_USER_SALES_CRM_SHOW = USER_EXT+'sales-crm/show/:id'
export const ROUTE_USER_CAMPAIGNS = USER_EXT+'campaigns'
export const ROUTE_USER_LISTS = USER_EXT+'lists'
export const ROUTE_USER_TAGS = USER_EXT+'tags'
export const ROUTE_USER_TAGS_CREATE = USER_EXT+'tags/create/:id?'
export const ROUTE_USER_TEMPLATES = USER_EXT+'templates'
export const ROUTE_USER_TEMPLATES_CREATE = USER_EXT+'templates/create/:id?'
export const ROUTE_USER_LISTS_SHOW = USER_EXT+'lists/show/:id'
export const ROUTE_USER_CAMPAIGNS_SHOW = USER_EXT+'campaigns/show/:id'
export const ROUTE_USER_CAMPAIGNS_CREATE = USER_EXT+'campaigns/create'
export const ROUTE_USER_CAMPAIGNS_UPDATE = USER_EXT+'campaigns/update/:id/:step?'

export const ROUTE_USER_CAMPAIGNS_STEP_1 = USER_EXT+'campaigns/step_1/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_2 = USER_EXT+'campaigns/step_2/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_3 = USER_EXT+'campaigns/step_3/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_4 = USER_EXT+'campaigns/step_4/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_5 = USER_EXT+'campaigns/step_5/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_6 = USER_EXT+'campaigns/step_6/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_7 = USER_EXT+'campaigns/step_7/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_8 = USER_EXT+'campaigns/step_8/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_9 = USER_EXT+'campaigns/step_9/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_10 = USER_EXT+'campaigns/step_10/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_11 = USER_EXT+'campaigns/step_11/:id?'
export const ROUTE_USER_CAMPAIGNS_STEP_12 = USER_EXT+'campaigns/step_12/:id?'
export const ROUTE_USER_CAMPAIGNS_ADD_MORE_LEADS = USER_EXT+'campaigns/add-more-leads/:id'

export const ROUTE_USER_PROFILE = `${USER_EXT}settings/profile`;
export const ROUTE_USER_BLACKLIST= `${USER_EXT}blacklists`;

export const ROUTE_USER_CHANGE_PASSWORD = `${USER_EXT}settings/change-password`;
export const ROUTE_USER_CHANGE_PROFILE = `${USER_EXT}settings/change-profile`;
export const ROUTE_USER_EMAIL_ACCOUNTS = `${USER_EXT}settings/email-accounts`;
export const ROUTE_USER_API_KEYS = `${USER_EXT}settings/api-keys`;
export const ROUTE_USER_API_DOMAINS = `${USER_EXT}settings/domains`;
export const ROUTE_USER_API_SUBSCRIPTIONS = `${USER_EXT}settings/subscriptions`;
export const ROUTE_USER_BILLING_INFORMATION = `${USER_EXT}settings/billing-information`;
export const ROUTE_USER_HELP = `${USER_EXT}settings/help`;
export const ROUTE_USER_INACTIVE = `${USER_EXT}inactive`;
export const ROUTE_USER_RESTRICTED = `${USER_EXT}restricted`;


/*-----------------------------------------------*/

export const formatStamp=function (stamp,format){
    if (stamp){
        return moment(stamp).format(format);
    }
    return '';
}


export const NOTIFY_TYPE_SUCCESS='success';
export const NOTIFY_TYPE_ERROR='error';
export const NOTIFY_TYPES={
    NOTIFY_TYPE_SUCCESS,
    NOTIFY_TYPE_ERROR,
};


export const notify = (message, options = {autoClose: 3000},type=NOTIFY_TYPE_SUCCESS) => {
    if (type === NOTIFY_TYPE_SUCCESS){
        toast.success(message,options);
    }else if (type === NOTIFY_TYPE_ERROR){
        toast.error(message,options);
    }else{
        toast(message, options);
    }
};



export const authorize = (slug,permissions) => {
    if (permissions.includes(slug)){
        return true;
    }
    else{
        return router.push(ROUTE_UNAUTHORIZED);
    }
}
export const getDateFromCreateAt = (stamp) => {
    if (!stamp) return '';
    return stamp.split('T')[0];
};
export const confirmDialog = (title, message) => {
    return Dialog.create({
        title: title,
        message: message,
        ok: { label: 'Yes', color: 'negative' ,class: ' w-25 py-1 px-3' },
        cancel: { label: 'No', color: 'secondary' ,class: 'w-25 py-1 px-3' },
        persistent: true,
        classes: 'custom-dialog',
    });
};
export const FORM_DATA = (data) => {
    const formData = new FormData();
    const appendFormData = (formData, key, value) => {
        if (value instanceof Array) {
            value.forEach((v, index) => {
                appendFormData(formData, `${key}[${index}]`, v);
            });
        } else if (value instanceof Object && !(value instanceof File)) {
            for (const subKey in value) {
                if (value.hasOwnProperty(subKey)) {
                    appendFormData(formData, `${key}[${subKey}]`, value[subKey]);
                }
            }
        } else {
            formData.append(key, value !== undefined && value !== null ? value : '');
        }
    };

    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            appendFormData(formData, key, data[key]);
        }
    }

    return formData;
};




export const LOGOUT=function (_this){
    _this.axios.post('logout').then(() => { }).finally(() => {
        localStorage.removeItem('user');
        localStorage.removeItem('impersonation_token');
        localStorage.removeItem('sanctum_token');
        _this.$router.push('/login');
    });
}
export const STANDARD_DATE_TIME_FORMAT='MMM D, Y - h:m A';
export const DATE_FORMAT='MMM D, Y';
export  const CURRENT_MONTH=function (){
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // getMonth() returns 0-indexed month
    return `${year}-${month}`;
}


export const SHEET_TYPE_CSV='csv';
export const SHEET_TYPE_GOOGLE='google';

export const CAMPAIGN_STATUS_DRAFT=0;
export const CAMPAIGN_STATUS_LAUNCHED=1;
export const CAMPAIGN_STATUS_PAUSED=2;
export const CAMPAIGN_STATUS_COMPLETED=3;

export const LIST_STATUS_DRAFTED=0;


export const LEAD_COLUMNS=25;


export const number_format=function (number){
    if (number){
        return String(number).replace(/(.)(?=(\d{3})+$)/g,'$1,');
    }
    return '';
}

export const LEAD_STATUS_NOT_CONTACTED=0;
export const LEAD_STATUS_CONTACTED=1;


export const EMAIL_TYPE_GOOGLE='google';
export const EMAIL_TYPE_SMTP='smtp';

export const SPAM_WORDS=
    [
        "Act", "Risk Free", "Offer", "Action", "Apply", "Buy", "Guaranteed", "Click", "Link", "Trial",
        "Act Now", "Affordable", "Amazing", "Apply Now", "Attention", "Best Price", "Buy Now", "Cancel",
        "Cash", "Cheap", "Click Below", "Clearance", "Congratulations", "Deal", "Discount", "Double Your Income",
        "Earn Extra Cash", "Easy", "Exclusive Offer", "Extra Income", "Fast Cash", "Financial Freedom",
        "Free", "Guarantee", "Hidden", "Incredible", "Instant", "Limited Time", "Make Money", "Money Back",
        "No Credit Check", "No Hidden Costs", "No Obligation", "One Time", "Opportunity", "Please Read", "Prize",
        "Profits", "Promise", "Risk-Free", "Sale", "Save", "Special", "Success", "Urgent", "Valuable", "Win",
        "Winner", "Act Immediately", "All New", "Apply Now!", "Attention Required", "Be Your Own Boss",
        "Best Price Guaranteed", "Big Earnings", "Call Now", "Cash Bonus", "Check or Money Order", "Collect",
        "Compare", "Confidential", "Cost", "Credit Card Offers", "Dear Friend", "Direct Marketing",
        "Double Your Cash", "Easy Terms", "Exclusive Deal", "Extra Cash", "Fast Results", "Financial Independence",
        "For Free", "For Instant Access", "Free Access", "Get Paid", "Hidden Charges", "Important Information Regarding",
        "Increase Sales", "Instant Cash", "Join Millions", "Loans", "Lowest Price", "Make Money Fast", "Million Dollars",
        "Money-Back Guarantee", "No Catch", "No Fees", "No Hidden Fees", "No Questions Asked", "Not Spam",
        "Once in a Lifetime", "Order Now", "Outstanding Values", "Performance", "Please Help", "Priority", "Pure Profit",
        "Refinance", "Risk-Free Trial", "Satisfaction Guaranteed", "Save Big Money", "See for Yourself", "Serious Cash",
        "Special Promotion", "Stop", "Take Action", "The Best", "Unsecured Credit", "Urgent Response", "Want More?",
        "Act Today", "All Natural", "Apply Online", "Avoid Bankruptcy", "Be Your Own Boss!", "Best Offer", "Big Savings",
        "Call Now!", "Cash Prize", "Check", "Collect Now", "Confidentiality", "Costs", "Credit Card Deals", "Dear Beloved",
        "Direct Email", "Double Your Money", "Easy Money", "Exclusive Opportunity", "Extra Income Potential", "Fast and Easy",
        "Financial Freedom!", "For Instant Cash", "Free Bonus", "Get Started Now", "Hidden Fees", "Important Notification",
        "Increase Your Sales", "Instant Access", "Join Now", "Lowest Prices", "Make Money Online", "Millionaire",
        "Money-Back Guarantee!", "No Credit", "No Fees!", "No Hidden Fees!", "No Risk", "Not a Scam", "One Time Only",
        "Order Today", "Outstanding Offer", "Personal Finance", "Please Respond", "Private", "Refinancing", "Risk-Free Offer",
        "Satisfaction Guaranteed!", "Save Money", "See This", "Serious Money", "Special Offers", "Stop Now", "Take Action Now",
        "The Best Deal", "Unsecured Debt", "Urgent Message", "Want to Earn More?", "Additional Income", "Amazing Offer",
        "Apply Today", "Bad Credit", "Best Price Online", "Big Discounts", "Call Now for Free", "Cash Incentive",
        "Check or Cash", "Collect Your Cash", "Confidential Message", "Credit Card Debt", "Dear Customer", "Direct Mail",
        "Double Your Pay", "Easy Approval", "Exclusive Price", "Extra Money", "Fast and Simple", "Financial Security",
        "For Free!", "Free Consultation", "Get Paid Fast", "Hidden Terms", "Important Information Inside", "Increase Your Profits",
        "Instant Approval", "Join Today", "Lowest Rate", "Make Quick Money", "Millionaire Mindset", "Money-Back Guarantee***",
        "No Credit Check!", "No Hidden Charges", "No Strings Attached", "Only Today", "Order Yours Now", "Outstanding Results",
        "Personal Loans", "Please Help Me", "Private and Confidential", "Refund", "Risk-Free Purchase", "Save Now",
        "See for Yourself!", "Serious Offer", "Special Discounts", "Stop Wasting Time", "Take Advantage", "The Best Solution",
        "Unsecured Loan", "Urgent Notice", "Want to Make Money?", "Affordable Price", "Amazing Opportunity", "Apply Now and Save",
        "Bankruptcy", "Best Rates", "Big Profits", "Call Toll-Free", "Cash Now", "Check It Out", "Collect Your Prize",
        "Confidentiality Guaranteed", "Credit Card Fraud", "Dear Friend!", "Direct Response", "Double Your Savings",
        "Easy Cash", "Exclusive Access", "Extra Cash Flow", "Fast Delivery", "Financial Success", "For Only", "Free Demo",
        "Get Paid Today", "Hidden Terms and Conditions", "Important Notice", "Increase Your Revenue", "Instant Money",
        "Join Us", "Lowest Rates", "Make Serious Money", "Millionaire Secrets", "Money-Back Policy", "No Deposit",
        "No Hidden Costs!", "No Strings Attached!", "Only a Few Left", "Order Yours Today", "Outstanding Service",
        "Personalized Offers", "Please Respond ASAP", "Refund Guaranteed", "Risk-Free Purchase!", "Save Money Now",
        "See How It Works", "Serious Buyers Only", "Special Invitation", "Stop Wasting Money", "Take Control",
        "The Best Value", "Unsecured Loan Offer", "Urgent Response Needed", "Want to Earn Extra Money?", "Affordable Rates",
        "Amazing Results", "Apply Today and Get", "Be Your Own Boss Today", "Best Services", "Big Winners",
        "Call Toll-Free Now", "Cash Prizes", "Check Out Now", "Collect Your Reward", "Confidential and Secure",
        "Credit Card Scam", "Dear Sir/Madam", "Direct Sales", "Double Your Success", "Easy Earnings", "Exclusive Content",
        "Extra Income Stream", "Fast Service", "Financial Growth", "For Your Eyes Only", "Free Download",
        "Get Rich Quick", "High Returns", "Important Announcement", "Increase Your Savings", "Instant Profit",
        "Join Today and Save", "Lowest Interest Rates", "Make Thousands", "Millionaire Lifestyle", "Money-Back Warranty",
        "No Down Payment", "No Hidden Fees Guaranteed", "Only for You", "Order Your Copy Now", "Outstanding Value",
        "Personalized Solutions", "Please Respond Immediately", "Refund Policy", "Risk-Free Trial!", "Save Money Today",
        "See Results Fast", "Serious Inquiries Only", "Special Limited Offer", "Stop Wasting Your Money", "Take Immediate Action",
        "The Best Investment", "Unsecured Personal Loan", "Urgent Reply Required", "Want to Make Extra Money?"
    ];

export const EMAIL_FORMAT_FIRST_DOT_LAST = 'first.last';
export const EMAIL_FORMAT_FIRST_DASH_LAST = 'first-last';
export const EMAIL_FORMAT_FIRST_LAST = 'firstlast';

export const EMAIL_FORMAT_F_DOT_LAST = 'f.last';
export const EMAIL_FORMAT_F_DASH_LAST = 'f-last';
export const EMAIL_FORMAT_F_LAST = 'flast';

export const EMAIL_FORMAT_LAST_DOT_FIRST = 'last.first';
export const EMAIL_FORMAT_LAST_DASH_FIRST = 'last-first';
export const EMAIL_FORMAT_LAST_FIRST = 'lastfirst';

export const EMAIL_FORMAT_L_DOT_FIRST = 'l.first';
export const EMAIL_FORMAT_L_DASH_FIRST = 'l-first';
export const EMAIL_FORMAT_L_FIRST = 'lfirst';

export const EMAIL_FORMAT_FIRST = 'first';
export const EMAIL_FORMAT_LAST = 'last';


export const EMAIL_FORMATS = [
    EMAIL_FORMAT_FIRST_DOT_LAST,
    EMAIL_FORMAT_FIRST_DASH_LAST,
    EMAIL_FORMAT_FIRST_LAST,

    EMAIL_FORMAT_F_DOT_LAST,
    EMAIL_FORMAT_F_DASH_LAST,
    EMAIL_FORMAT_F_LAST,

    EMAIL_FORMAT_LAST_DOT_FIRST,
    EMAIL_FORMAT_LAST_DASH_FIRST,
    EMAIL_FORMAT_LAST_FIRST,

    EMAIL_FORMAT_L_DOT_FIRST,
    EMAIL_FORMAT_L_DASH_FIRST,
    EMAIL_FORMAT_L_FIRST,

    EMAIL_FORMAT_FIRST,
    EMAIL_FORMAT_LAST,
];
export const CURRENCY_CODES=[
    "USD",
"AED",
"AFN",
"ALL",
"AMD",
"ANG",
"AOA",
"ARS",
"AUD",
"AWG",
"AZN",
"BAM",
"BBD",
"BDT",
"BGN",
"BIF",
"BMD",
"BND",
"BOB",
"BRL",
"BSD",
"BWP",
"BYN",
"BZD",
"CAD",
"CDF",
"CHF",
"CLP",
"CNY",
"COP",
"CRC",
"CVE",
"CZK",
"DJF",
"DKK",
"DOP",
"DZD",
"EGP",
"ETB",
"EUR",
"FJD",
"FKP",
"GBP",
"GEL",
"GIP",
"GMD",
"GNF",
"GTQ",
"GYD",
"HKD",
"HNL",
"HTG",
"HUF",
"IDR",
"ILS",
"INR",
"ISK",
"JMD",
"JPY",
"KES",
"KGS",
"KHR",
"KMF",
"KRW",
"KYD",
"KZT",
"LAK",
"LBP",
"LKR",
"LRD",
"LSL",
"MAD",
"MDL",
"MGA",
"MKD",
"MMK",
"MNT",
"MOP",
"MUR",
"MVR",
"MWK",
"MXN",
"MYR",
"MZN",
"NAD",
"NGN",
"NIO",
"NOK",
"NPR",
"NZD",
"PAB",
"PEN",
"PGK",
"PHP",
"PKR",
"PLN",
"PYG",
"QAR",
"RON",
"RSD",
"RUB",
"RWF",
"SAR",
"SBD",
"SCR",
"SEK",
"SGD",
"SHP",
"SLE",
"SOS",
"SRD",
"STD",
"SZL",
"THB",
"TJS",
"TOP",
"TRY",
"TTD",
"TWD",
"TZS",
"UAH",
"UGX",
"UYU",
"UZS",
"VND",
"VUV",
"WST",
"XAF",
"XCD",
"XCG",
"XOF",
"XPF",
"YER",
"ZAR",
"ZMW"

];

export const COUNTRIES=[
    "Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia","Cameroon","Canada","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Congo-Brazzaville)","Costa Rica","Côte d'Ivoire","Croatia","Cuba","Cyprus","Czechia (Czech Republic)","Democratic Republic of the Congo","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini (fmr. \"Swaziland\")","Ethiopia","Fiji","Finland","France","Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Holy See","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar (formerly Burma)","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway","Oman","Pakistan","Palau","Palestine State","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea","South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Tajikistan","Tanzania","Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States of America","Uruguay","Uzbekistan","Vanuatu","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe"
];


export const SUBSCRIPTION_ACTIVE = 'active';
export const SUBSCRIPTION_PENDING = 'pending';
export const SUBSCRIPTION_REJECTED = 'rejected';
export const SUBSCRIPTION_EXPIRED = 'expired';
export const SUBSCRIPTION_CANCEL = 'cancelled';