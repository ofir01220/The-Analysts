import json
import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import seaborn as sns
import warnings

warnings.filterwarnings('ignore')

sns.set()
pd.set_option("display.precision", 2)
plt.rcParams.update({'font.size': 20, 'figure.figsize': (8, 4)})
# This is a sample Python script.

# Press Shift+F10 to execute it or replace it with your code.
# Press Double Shift to search everywhere for classes, files, tool windows, actions, and settings.
file_name = "readings-clean.csv"


def print_data_graph():
    with open(file_name, "r") as file:
        for line in file:
            measurement = json.loads(line)
            measurement_attribute = list(measurement.keys())[0]
            v_dict_arr = list(measurement.values())[0]
            try:
                match measurement_attribute:
                    case "dailies":
                        extract_data_print_graph(v_dict_arr, 'HeartRate', 'timeOffsetHeartRateSamples', 'heartrate_graphs_data_unsorted')
            except KeyError:
                print("Error@@@@")


def extract_data_print_graph(dict_arr, graph_type, samples_attribute_name, table_name):
    for i in range(len(dict_arr)):
        v_dict = dict_arr[i]
        samples_dict = v_dict.get(samples_attribute_name, {})
        samples_date = v_dict.get('calendarDate', '0')

        if len(samples_dict) != 0:
            heartrate_dict_keys = list(samples_dict.keys())
            heartrate_dict_values = list(samples_dict.values())
            heartrate_samples_df = pd.DataFrame({'time': heartrate_dict_keys, 'heartrate': heartrate_dict_values})
            # preparing plot title name
            plot_title_name = "userID: 404   Date: "+samples_date
            # creating lineplot
            sns.lineplot(x='time', y='heartrate', data=heartrate_samples_df, color='red')
            plt.title(plot_title_name)
            # configurating axis "x" bins
            # plt.xticks(np.arange(0, 25, step=1))

            plt.show()

        else:
            print("samples map was empty")


if __name__ == '__main__':
    print_data_graph()

