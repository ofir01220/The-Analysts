import sys
import smtplib
from email.message import EmailMessage
import pandas as pd
import pywhatkit


def send_reminders(i_email, i_phone_number):
    message_text_to_email = f",Hello \n" \
                            f".this is an automatic message from the clock experiment system \n" \
                            f".We detected that your watch has not been synchronized with the system for the last 2 days \n" \
                            f".Please enable the watch's synchronization function"
    message_text_to_phone = f"Hello, \n" \
                            f"this is an automatic message from the clock experiment system. \n" \
                            f"We detected that your watch has not been synchronized with the system for the last 2 days. \n" \
                            f"Please enable the watch's synchronization function."

    send_reminder_via_email(message_text_to_email, i_email)
    send_reminder_via_whatsapp(message_text_to_phone, i_phone_number)


def get_data_from_excel_via_number(i_target_number, i_column_name):
    try:
        data_frame = pd.read_excel("contact list.xlsx")

        for i in range(len(data_frame['מספר נבדק '].array)):
            if try_parse_int(data_frame['מספר נבדק '].array[i]) == int(i_target_number):
                receiver_data = data_frame[i_column_name].array[i]
                return receiver_data

        print(f"Data not found for user ID {i_target_number}.")
        return None
    except Exception as e:
        print("Error occurred:", e)
        return None


def get_email_and_phone():
    if len(sys.argv) != 2:
        print("Wrong input.")
        return
    tester_id = sys.argv[1]
    email = get_data_from_excel_via_number(tester_id, 'כתובת מייל')
    original_number = get_data_from_excel_via_number(tester_id, 'מספר טלפון')
    formatted_number = format_israeli_number(original_number)

    return email, formatted_number


def send_reminder_via_email(i_message_text, i_receiver_email):
    subject = "Sync reminder"
    msg = EmailMessage()
    msg.set_content(i_message_text)
    msg['subject'] = subject
    msg['to'] = i_receiver_email

    user = "desigpatter57@gmail.com"
    password = "qeoizhtqipjlywyv"
    msg['from'] = user

    try:
        server = smtplib.SMTP("smtp.gmail.com", 587)
        server.starttls()
        server.login(user, password)
        server.send_message(msg)
        server.quit()
        print(f"Email sent successfully to {i_receiver_email}")
    except Exception as e:
        print("An error occurred while sending the email:", e)


def send_reminder_via_whatsapp(i_message_text, i_phone_number):
    try:
        pywhatkit.sendwhatmsg_instantly(i_phone_number, i_message_text, 15, True, 2)
        print(f"WhatsApp message sent successfully to {i_phone_number}")
    except Exception as e:
        print("An error occurred while sending WhatsApp message:", e)


def try_parse_int(s):
    try:
        # Attempt to convert the string 's' to an integer
        value = int(s)
        return value
    except ValueError:
        # If 's' cannot be converted to an integer, return None
        return None


def format_israeli_number(i_phone_number):
    israel_code = "+972"
    formatted_number = f"{israel_code}-{i_phone_number}"

    return formatted_number


if __name__ == '__main__':
    email_address, phone_number = get_email_and_phone()
    send_reminders(email_address, phone_number)
